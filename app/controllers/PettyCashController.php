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
        $isAjax = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

        $name = trim($_POST['name'] ?? '');
        $classification = trim($_POST['classification'] ?? 'servicio');
        $category = trim($_POST['category'] ?? 'General');
        if (!in_array($classification, ['producto', 'servicio'], true)) {
            $classification = 'servicio';
        }
        $price = max(0, (float)($_POST['suggested_price'] ?? 0));
        $unitMeasure = trim($_POST['unit_measure'] ?? 'Unidad');
        $allowedUnits = ['Unidad', 'Kilo', 'Litro', 'Gramo', 'Metro', 'Mililitro', 'Centímetro'];
        if (!in_array($unitMeasure, $allowedUnits, true)) {
            $unitMeasure = 'Unidad';
        }

        if ($name === '') {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'Debes indicar nombre del producto.']);
                exit;
            }
            flash('error', 'Debes indicar nombre del producto.');
            $this->redirect('index.php?route=petty-cash/create');
        }

        try {
            $productId = $this->products->create([
                'company_id' => $companyId,
                'name' => $name,
                'classification' => $classification,
                'category' => $category,
                'suggested_price' => $price,
                'unit_measure' => $unitMeasure,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => true,
                    'message' => 'Producto de caja chica creado correctamente.',
                    'product' => [
                        'id' => $productId,
                        'name' => $name,
                        'category' => $category,
                        'suggested_price' => $price,
                        'unit_measure' => $unitMeasure,
                    ],
                ]);
                exit;
            }

            flash('success', 'Producto de caja chica creado correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Error creando producto caja chica: ' . $e->getMessage());

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode(['ok' => false, 'message' => 'No se pudo crear el producto.']);
                exit;
            }

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

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="caja_chica_' . date('Ymd_His') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<html><head><meta charset="UTF-8">';
        echo '<style>';
        echo 'table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;}';
        echo 'th,td{border:1px solid #777;padding:6px 8px;}';
        echo 'th{background:#e6e6e6;font-weight:bold;text-align:center;}';
        echo '.text-right{text-align:right;}';
        echo '.text-center{text-align:center;}';
        echo '</style>';
        echo '</head><body>';

        echo '<table>';
        echo '<thead><tr>';
        echo '<th>N°</th>';
        echo '<th>Fecha</th>';
        echo '<th>N° Boleta</th>';
        echo '<th>Proveedor</th>';
        echo '<th>Detalle</th>';
        echo '<th>Cant.</th>';
        echo '<th>Valor Unit.</th>';
        echo '<th>Valor</th>';
        echo '<th>Obs.</th>';
        echo '</tr></thead><tbody>';

        $rowNumber = 1;
        foreach ($receipts as $receipt) {
            $items = $this->items->byReceipt((int)$receipt['id']);
            if (empty($items)) {
                $items = [[
                    'description' => '-',
                    'quantity' => 0,
                    'unit_price' => 0,
                    'subtotal' => 0,
                    'observation' => '',
                ]];
            }

            foreach ($items as $item) {
                $date = (string)($receipt['receipt_date'] ?? '');
                $date = $date !== '' ? date('d.m.Y', strtotime($date)) : '';
                $boleta = htmlspecialchars((string)($receipt['receipt_number'] ?? ''), ENT_QUOTES, 'UTF-8');
                $supplier = htmlspecialchars((string)($receipt['supplier_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                $detail = htmlspecialchars((string)($item['description'] ?? ''), ENT_QUOTES, 'UTF-8');
                $obs = htmlspecialchars((string)($item['observation'] ?? ''), ENT_QUOTES, 'UTF-8');
                $qty = number_format((float)($item['quantity'] ?? 0), 2, ',', '.');
                $unitPrice = number_format((float)($item['unit_price'] ?? 0), 0, ',', '.');
                $subtotal = number_format((float)($item['subtotal'] ?? 0), 0, ',', '.');

                echo '<tr>';
                echo '<td class="text-center">' . $rowNumber . '</td>';
                echo '<td>' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . $boleta . '</td>';
                echo '<td>' . $supplier . '</td>';
                echo '<td>' . $detail . '</td>';
                echo '<td class="text-right">' . $qty . '</td>';
                echo '<td class="text-right">$ ' . $unitPrice . '</td>';
                echo '<td class="text-right">$ ' . $subtotal . '</td>';
                echo '<td>' . $obs . '</td>';
                echo '</tr>';
                $rowNumber++;
            }
        }

        echo '</tbody></table>';
        echo '</body></html>';
        exit;
    }
}
