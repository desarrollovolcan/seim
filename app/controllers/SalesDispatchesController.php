<?php

class SalesDispatchesController extends Controller
{
    private SalesDispatchesModel $dispatches;
    private SalesDispatchItemsModel $items;
    private ProducedProductsModel $producedProducts;
    private PosSessionsModel $posSessions;
    private UsersModel $users;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->dispatches = new SalesDispatchesModel($db);
        $this->items = new SalesDispatchItemsModel($db);
        $this->producedProducts = new ProducedProductsModel($db);
        $this->posSessions = new PosSessionsModel($db);
        $this->users = new UsersModel($db);
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

    private function moduleReady(): bool
    {
        return table_exists($this->db, 'sales_dispatches') && table_exists($this->db, 'sales_dispatch_items');
    }

    private function hasSellerUserIdColumn(): bool
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
            ['table' => 'sales_dispatches', 'column' => 'seller_user_id']
        );
        return (int)($row['total'] ?? 0) > 0;
    }

    private function hasPosSaleContextColumn(): bool
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
            ['table' => 'pos_sessions', 'column' => 'sale_context']
        );
        return (int)($row['total'] ?? 0) > 0;
    }

    private function posBalanceForDispatch(array $dispatch, int $companyId): array
    {
        $sellerUserId = (int)($dispatch['seller_user_id'] ?? 0);
        if (!$sellerUserId || !table_exists($this->db, 'pos_sessions')) {
            return ['sessions' => [], 'totals' => ['opening' => 0.0, 'sales' => 0.0, 'withdrawals' => 0.0, 'closing' => 0.0]];
        }

        $date = (string)($dispatch['dispatch_date'] ?? date('Y-m-d'));
        $contextFilter = $this->hasPosSaleContextColumn() ? 'AND ps.sale_context = "camion"' : '';

        $sessions = $this->db->fetchAll(
            "SELECT ps.*, 
                    COALESCE(sales.total, 0) AS sales_total,
                    COALESCE(withdrawals.total, 0) AS withdrawals_total
             FROM pos_sessions ps
             LEFT JOIN (
                SELECT s.pos_session_id, SUM(sp.amount) AS total
                FROM sales s
                INNER JOIN sale_payments sp ON sp.sale_id = s.id
                GROUP BY s.pos_session_id
             ) sales ON sales.pos_session_id = ps.id
             LEFT JOIN (
                SELECT pos_session_id, SUM(amount) AS total
                FROM pos_session_withdrawals
                GROUP BY pos_session_id
             ) withdrawals ON withdrawals.pos_session_id = ps.id
             WHERE ps.company_id = :company_id
               AND ps.user_id = :user_id
               AND DATE(ps.opened_at) = :dispatch_date
               {$contextFilter}
             ORDER BY ps.opened_at ASC",
            [
                'company_id' => $companyId,
                'user_id' => $sellerUserId,
                'dispatch_date' => $date,
            ]
        );

        $totals = ['opening' => 0.0, 'sales' => 0.0, 'withdrawals' => 0.0, 'closing' => 0.0];
        foreach ($sessions as $session) {
            $totals['opening'] += (float)($session['opening_amount'] ?? 0);
            $totals['sales'] += (float)($session['sales_total'] ?? 0);
            $totals['withdrawals'] += (float)($session['withdrawals_total'] ?? 0);
            $totals['closing'] += (float)($session['closing_amount'] ?? 0);
        }

        return ['sessions' => $sessions, 'totals' => $totals];
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        if (!$this->moduleReady()) {
            flash('error', 'Faltan tablas del módulo Control Camiones. Ejecuta la migración bd/actualizacion_20260205_despacho_camiones.sql');
            $this->redirect('index.php?route=dashboard');
        }

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
        if (!$this->moduleReady()) {
            flash('error', 'Faltan tablas del módulo Control Camiones. Ejecuta la migración bd/actualizacion_20260205_despacho_camiones.sql');
            $this->redirect('index.php?route=dashboard');
        }

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
        if (!$this->moduleReady()) {
            flash('error', 'Faltan tablas del módulo Control Camiones. Ejecuta la migración bd/actualizacion_20260205_despacho_camiones.sql');
            $this->redirect('index.php?route=dashboard');
        }

        $sellerUsers = $this->users->allActive($companyId);
        $dispatches = $this->dispatches->listWithRelations($companyId);

        $this->render('sales/dispatches/create', [
            'title' => 'Nuevo despacho',
            'pageTitle' => 'Nuevo despacho de camión',
            'producedProducts' => $this->producedProducts->active($companyId),
            'sellerUsers' => $sellerUsers,
            'today' => date('Y-m-d'),
            'recentDispatches' => array_slice($dispatches, 0, 20),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        if (!$this->moduleReady()) {
            flash('error', 'No se puede guardar el despacho: faltan tablas del módulo Control Camiones.');
            $this->redirect('index.php?route=sales/dispatches/create');
        }

        $truckCode = trim($_POST['truck_code'] ?? '');
        $sellerUserId = (int)($_POST['seller_user_id'] ?? 0);
        $dispatchDate = trim($_POST['dispatch_date'] ?? '') ?: date('Y-m-d');
        $notes = trim($_POST['notes'] ?? '');

        if ($truckCode === '' || $sellerUserId <= 0) {
            flash('error', 'Debes ingresar camión y seleccionar vendedor usuario.');
            $this->redirect('index.php?route=sales/dispatches/create');
        }

        $sellerUser = $this->db->fetch(
            'SELECT id, name FROM users WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $sellerUserId, 'company_id' => $companyId]
        );
        if (!$sellerUser) {
            flash('error', 'El vendedor seleccionado no es válido para la empresa.');
            $this->redirect('index.php?route=sales/dispatches/create');
        }
        $sellerName = trim((string)($sellerUser['name'] ?? ''));
        if ($sellerName === '') {
            flash('error', 'El vendedor seleccionado no tiene nombre válido.');
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
            $data = [
                'company_id' => $companyId,
                'truck_code' => $truckCode,
                'seller_name' => $sellerName,
                'dispatch_date' => $dispatchDate,
                'pos_session_id' => null,
                'status' => 'abierto',
                'notes' => $notes,
                'cash_delivered' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($this->hasSellerUserIdColumn()) {
                $data['seller_user_id'] = $sellerUserId;
            }

            $dispatchId = $this->dispatches->create($data);

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

            $savedDispatch = $this->db->fetch(
                'SELECT id FROM sales_dispatches WHERE id = :id AND company_id = :company_id',
                ['id' => (int)$dispatchId, 'company_id' => $companyId]
            );
            $savedItemsCount = $this->db->fetch(
                'SELECT COUNT(*) AS total FROM sales_dispatch_items WHERE dispatch_id = :dispatch_id',
                ['dispatch_id' => (int)$dispatchId]
            );
            if (!$savedDispatch || (int)($savedItemsCount['total'] ?? 0) <= 0) {
                throw new RuntimeException('No se confirmó la persistencia completa del despacho en la base de datos.');
            }

            flash('success', 'Despacho guardado correctamente. ID #' . (int)$dispatchId);
            $this->redirect('index.php?route=sales/dispatches/create');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            log_message('error', 'No se pudo crear despacho: ' . $e->getMessage());
            flash('error', 'No se pudo crear el despacho: ' . $e->getMessage());
            $this->redirect('index.php?route=sales/dispatches/create');
        }
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        if (!$this->moduleReady()) {
            flash('error', 'Faltan tablas del módulo Control Camiones.');
            $this->redirect('index.php?route=dashboard');
        }

        $dispatch = $this->dispatches->findWithRelations($id, $companyId);
        if (!$dispatch) {
            flash('error', 'Despacho no encontrado.');
            $this->redirect('index.php?route=sales/dispatches');
        }

        $items = $this->items->byDispatch($id);
        $totals = ['dispatched' => 0, 'returned' => 0, 'sold' => 0, 'merma' => 0];
        foreach ($items as &$item) {
            $dispatched = (int)($item['quantity_dispatched'] ?? 0);
            $returned = (int)($item['empty_returned_total'] ?? 0);
            $sold = max(0, $dispatched - $returned);
            $item['sold_quantity'] = $sold;
            $totals['dispatched'] += $dispatched;
            $totals['returned'] += $returned;
            $totals['sold'] += $sold;
            $totals['merma'] += (int)($item['empty_merma'] ?? 0);
        }
        unset($item);

        $posBalance = $this->posBalanceForDispatch($dispatch, $companyId);
        $expectedCash = (float)($posBalance['totals']['closing'] ?? 0);
        $deliveredCash = (float)($dispatch['cash_delivered'] ?? 0);
        $cashDiff = $deliveredCash - $expectedCash;
        $cashStatus = 'cuadrado';
        if ($cashDiff < -0.009) {
            $cashStatus = 'falta';
        } elseif ($cashDiff > 0.009) {
            $cashStatus = 'sobra';
        }

        $this->render('sales/dispatches/show', [
            'title' => 'Detalle despacho',
            'pageTitle' => 'Detalle despacho ' . $dispatch['truck_code'],
            'dispatch' => $dispatch,
            'items' => $items,
            'itemTotals' => $totals,
            'posBalance' => $posBalance,
            'cashExpected' => $expectedCash,
            'cashDiff' => $cashDiff,
            'cashStatus' => $cashStatus,
        ]);
    }

    public function close(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        if (!$this->moduleReady()) {
            flash('error', 'Faltan tablas del módulo Control Camiones.');
            $this->redirect('index.php?route=dashboard');
        }

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
            $itemRow = $this->db->fetch(
                'SELECT id, quantity_dispatched FROM sales_dispatch_items WHERE id = :id AND dispatch_id = :dispatch_id',
                ['id' => $itemId, 'dispatch_id' => $id]
            );
            if (!$itemRow) {
                continue;
            }
            $dispatched = (int)($itemRow['quantity_dispatched'] ?? 0);

            $ret = max(0, (int)($retTotals[$i] ?? 0));
            $mb = max(0, (int)($muyBueno[$i] ?? 0));
            $b = max(0, (int)($bueno[$i] ?? 0));
            $a = max(0, (int)($aceptable[$i] ?? 0));
            $m = max(0, (int)($malo[$i] ?? 0));
            $me = max(0, (int)($merma[$i] ?? 0));

            $sumStates = $mb + $b + $a + $m + $me;
            if ($ret <= 0 && $sumStates > 0) {
                $ret = $sumStates;
            } elseif ($ret > 0 && $sumStates === 0) {
                $a = $ret;
                $sumStates = $ret;
            } elseif ($sumStates < $ret) {
                $a += ($ret - $sumStates);
                $sumStates = $ret;
            }

            if ($sumStates > $ret) {
                flash('error', 'La suma por estado no puede superar el total retornado en cada producto.');
                $this->redirect('index.php?route=sales/dispatches/show&id=' . $id);
            }
            if ($ret > $dispatched) {
                flash('error', 'El total retornado no puede superar la cantidad despachada.');
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

        flash('success', 'Cierre de despacho registrado correctamente. Se calculó balance de productos y dinero.');
        $this->redirect('index.php?route=sales/dispatches/show&id=' . $id);
    }
}
