<?php

class SalesDispatchesController extends Controller
{
    private SalesDispatchesModel $dispatches;
    private SalesDispatchItemsModel $items;
    private ProducedProductsModel $producedProducts;
    private PosSessionsModel $posSessions;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->dispatches = new SalesDispatchesModel($db);
        $this->items = new SalesDispatchItemsModel($db);
        $this->producedProducts = new ProducedProductsModel($db);
        $this->posSessions = new PosSessionsModel($db);
    }

    private function requireCompany(): int
    {
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        return (int)$companyId;
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('sales/dispatches/index', [
            'title' => 'Despachos de camiones',
            'pageTitle' => 'Despachos de camiones',
            'dispatches' => $this->dispatches->listWithRelations($companyId),
        ]);
    }


    public function reception(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $dispatches = $this->dispatches->listWithRelations($companyId);
        $openDispatches = array_values(array_filter($dispatches, static fn(array $row): bool => ($row['status'] ?? '') === 'abierto'));
        $closedDispatches = array_values(array_filter($dispatches, static fn(array $row): bool => ($row['status'] ?? '') === 'cerrado'));

        $this->render('sales/dispatches/reception', [
            'title' => 'Recepción camiones vendedores',
            'pageTitle' => 'Recepción de camiones vendedores',
            'openDispatches' => $openDispatches,
            'closedDispatches' => $closedDispatches,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('sales/dispatches/create', [
            'title' => 'Nuevo despacho',
            'pageTitle' => 'Nuevo despacho de camión',
            'producedProducts' => $this->producedProducts->active($companyId),
            'sessions' => $this->posSessions->all('company_id = :company_id', ['company_id' => $companyId]),
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $truckCode = trim($_POST['truck_code'] ?? '');
        $sellerName = trim($_POST['seller_name'] ?? '');
        $dispatchDate = trim($_POST['dispatch_date'] ?? date('Y-m-d'));
        $posSessionId = (int)($_POST['pos_session_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        if ($truckCode === '' || $sellerName === '') {
            flash('error', 'Debes ingresar camión y vendedor.');
            $this->redirect('index.php?route=sales/dispatches/create');
        }

        $productIds = $_POST['produced_product_id'] ?? [];
        $quantities = $_POST['quantity_dispatched'] ?? [];
        $items = [];
        foreach ($productIds as $i => $productIdRaw) {
            $productId = (int)$productIdRaw;
            $qty = (int)($quantities[$i] ?? 0);
            if ($productId <= 0 || $qty <= 0) {
                continue;
            }
            $product = $this->producedProducts->findForCompany($productId, $companyId);
            if (!$product) {
                continue;
            }
            $items[] = ['produced_product_id' => $productId, 'quantity_dispatched' => $qty];
        }

        if (empty($items)) {
            flash('error', 'Debes agregar al menos un producto fabricado con cantidad despachada.');
            $this->redirect('index.php?route=sales/dispatches/create');
        }

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $dispatchId = $this->dispatches->create([
                'company_id' => $companyId,
                'truck_code' => $truckCode,
                'seller_name' => $sellerName,
                'dispatch_date' => $dispatchDate,
                'pos_session_id' => $posSessionId > 0 ? $posSessionId : null,
                'status' => 'abierto',
                'notes' => $notes,
                'cash_delivered' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($items as $item) {
                $this->items->create([
                    'dispatch_id' => $dispatchId,
                    'produced_product_id' => $item['produced_product_id'],
                    'quantity_dispatched' => $item['quantity_dispatched'],
                    'empty_returned_total' => 0,
                    'empty_muy_bueno' => 0,
                    'empty_bueno' => 0,
                    'empty_aceptable' => 0,
                    'empty_malo' => 0,
                    'empty_merma' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $pdo->commit();
            flash('success', 'Despacho creado. Registra retorno y dinero cuando vuelva el camión.');
            $this->redirect('index.php?route=sales/dispatches/show&id=' . $dispatchId);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            log_message('error', 'No se pudo crear despacho: ' . $e->getMessage());
            flash('error', 'No se pudo crear el despacho.');
            $this->redirect('index.php?route=sales/dispatches/create');
        }
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);

        $dispatch = $this->dispatches->findWithRelations($id, $companyId);
        if (!$dispatch) {
            flash('error', 'Despacho no encontrado.');
            $this->redirect('index.php?route=sales/dispatches');
        }

        $this->render('sales/dispatches/show', [
            'title' => 'Detalle despacho',
            'pageTitle' => 'Detalle despacho ' . $dispatch['truck_code'],
            'dispatch' => $dispatch,
            'items' => $this->items->byDispatch($id),
        ]);
    }

    public function close(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);

        $dispatch = $this->dispatches->findWithRelations($id, $companyId);
        if (!$dispatch) {
            flash('error', 'Despacho no encontrado.');
            $this->redirect('index.php?route=sales/dispatches');
        }

        $itemIds = $_POST['item_id'] ?? [];
        $retTotals = $_POST['empty_returned_total'] ?? [];
        $muyBueno = $_POST['empty_muy_bueno'] ?? [];
        $bueno = $_POST['empty_bueno'] ?? [];
        $aceptable = $_POST['empty_aceptable'] ?? [];
        $malo = $_POST['empty_malo'] ?? [];
        $merma = $_POST['empty_merma'] ?? [];

        foreach ($itemIds as $i => $itemIdRaw) {
            $itemId = (int)$itemIdRaw;
            $ret = max(0, (int)($retTotals[$i] ?? 0));
            $mb = max(0, (int)($muyBueno[$i] ?? 0));
            $b = max(0, (int)($bueno[$i] ?? 0));
            $a = max(0, (int)($aceptable[$i] ?? 0));
            $m = max(0, (int)($malo[$i] ?? 0));
            $me = max(0, (int)($merma[$i] ?? 0));

            $sumStates = $mb + $b + $a + $m + $me;
            if ($sumStates !== $ret) {
                flash('error', 'La suma por estado debe coincidir con envases retornados en cada producto.');
                $this->redirect('index.php?route=sales/dispatches/show&id=' . $id);
            }

            $this->items->update($itemId, [
                'empty_returned_total' => $ret,
                'empty_muy_bueno' => $mb,
                'empty_bueno' => $b,
                'empty_aceptable' => $a,
                'empty_malo' => $m,
                'empty_merma' => $me,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $cashDelivered = max(0, (float)($_POST['cash_delivered'] ?? 0));
        $this->dispatches->update($id, [
            'cash_delivered' => $cashDelivered,
            'status' => 'cerrado',
            'closed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        flash('success', 'Cierre de despacho registrado correctamente.');
        $this->redirect('index.php?route=sales/dispatches/show&id=' . $id);
    }
}
