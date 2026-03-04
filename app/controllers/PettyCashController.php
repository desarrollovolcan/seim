<?php

class PettyCashController extends Controller
{
    private PettyCashProductsModel $products;
    private PettyCashReceiptsModel $receipts;
    private PettyCashReceiptItemsModel $items;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->products = new PettyCashProductsModel($db);
        $this->receipts = new PettyCashReceiptsModel($db);
        $this->items = new PettyCashReceiptItemsModel($db);
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

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('petty-cash/create', [
            'title' => 'Caja chica',
            'pageTitle' => 'Registrar boleta de caja chica',
            'products' => $this->products->active($companyId),
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $receiptNumber = trim($_POST['receipt_number'] ?? '');
        $receiptDate = trim($_POST['receipt_date'] ?? date('Y-m-d'));
        $supplierName = trim($_POST['supplier_name'] ?? '');
        $currency = trim($_POST['currency'] ?? 'CLP');
        $notes = trim($_POST['notes'] ?? '');

        if ($receiptNumber === '' || $supplierName === '') {
            flash('error', 'Debes completar número de boleta y proveedor.');
            $this->redirect('index.php?route=petty-cash/create');
        }

        $itemProductIds = $_POST['item_product_id'] ?? [];
        $itemDescriptions = $_POST['item_description'] ?? [];
        $itemQuantities = $_POST['item_quantity'] ?? [];
        $itemPrices = $_POST['item_unit_price'] ?? [];
        $itemObservations = $_POST['item_observation'] ?? [];

        $rows = [];
        foreach ($itemDescriptions as $index => $description) {
            $description = trim((string)$description);
            $qty = max(0, (float)($itemQuantities[$index] ?? 0));
            $price = max(0, (float)($itemPrices[$index] ?? 0));
            $productId = (int)($itemProductIds[$index] ?? 0);
            $observation = trim((string)($itemObservations[$index] ?? ''));

            if ($description === '' && $productId > 0) {
                $product = $this->products->findForCompany($productId, $companyId);
                $description = $product['name'] ?? '';
            }

            if ($description === '' || $qty <= 0) {
                continue;
            }

            $rows[] = [
                'product_id' => $productId > 0 ? $productId : null,
                'description' => $description,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => round($qty * $price, 2),
                'observation' => $observation,
            ];
        }

        if (empty($rows)) {
            flash('error', 'Debes ingresar al menos un producto/ítem válido.');
            $this->redirect('index.php?route=petty-cash/create');
        }

        $total = array_sum(array_map(static fn(array $r): float => (float)$r['subtotal'], $rows));

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();

            $receiptId = $this->receipts->create([
                'company_id' => $companyId,
                'receipt_number' => $receiptNumber,
                'receipt_date' => $receiptDate,
                'supplier_name' => $supplierName,
                'currency' => $currency,
                'total_amount' => $total,
                'notes' => $notes,
                'created_by' => (int)(Auth::user()['id'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($rows as $row) {
                $this->items->create([
                    'receipt_id' => $receiptId,
                    'product_id' => $row['product_id'],
                    'description' => $row['description'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'subtotal' => $row['subtotal'],
                    'observation' => $row['observation'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            audit($this->db, Auth::user()['id'] ?? null, 'create', 'petty_cash_receipts', $receiptId);
            $pdo->commit();
            flash('success', 'Boleta de caja chica registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error guardando caja chica: ' . $e->getMessage());
            flash('error', 'No se pudo guardar la boleta.');
        }

        $this->redirect('index.php?route=petty-cash');
    }

    public function storeProduct(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $price = max(0, (float)($_POST['suggested_price'] ?? 0));

        if ($name === '') {
            flash('error', 'Debes indicar nombre del producto.');
            $this->redirect('index.php?route=petty-cash/create');
        }

        try {
            $this->products->create([
                'company_id' => $companyId,
                'name' => $name,
                'category' => $category,
                'suggested_price' => $price,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            flash('success', 'Producto de caja chica creado correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Error creando producto caja chica: ' . $e->getMessage());
            flash('error', 'No se pudo crear el producto.');
        }

        $this->redirect('index.php?route=petty-cash/create');
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $filters = [
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to' => trim($_GET['date_to'] ?? ''),
            'supplier' => trim($_GET['supplier'] ?? ''),
        ];

        $receipts = $this->receipts->listWithFilters($companyId, $filters);
        foreach ($receipts as &$receipt) {
            $receipt['items'] = $this->items->byReceipt((int)$receipt['id']);
        }
        unset($receipt);

        $this->render('petty-cash/index', [
            'title' => 'Caja chica',
            'pageTitle' => 'Listado de boletas caja chica',
            'filters' => $filters,
            'receipts' => $receipts,
        ]);
    }

    public function export(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $filters = [
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to' => trim($_GET['date_to'] ?? ''),
            'supplier' => trim($_GET['supplier'] ?? ''),
        ];
        $receipts = $this->receipts->listWithFilters($companyId, $filters);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="caja_chica_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['N° Boleta', 'Fecha', 'Proveedor', 'Moneda', 'Total', 'Items', 'Detalle']);

        foreach ($receipts as $receipt) {
            $items = $this->items->byReceipt((int)$receipt['id']);
            $detail = implode(' | ', array_map(static function (array $item): string {
                $obs = trim((string)($item['observation'] ?? ''));
                return ($item['description'] ?? '') . ' x' . (float)($item['quantity'] ?? 0) . ($obs !== '' ? ' (' . $obs . ')' : '');
            }, $items));

            fputcsv($output, [
                $receipt['receipt_number'] ?? '',
                $receipt['receipt_date'] ?? '',
                $receipt['supplier_name'] ?? '',
                $receipt['currency'] ?? 'CLP',
                (float)($receipt['total_amount'] ?? 0),
                (int)($receipt['items_count'] ?? 0),
                $detail,
            ]);
        }

        fclose($output);
        exit;
    }
}
