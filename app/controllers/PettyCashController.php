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


    private function normalizeAmount($value): float
    {
        $value = trim((string)$value);
        if ($value === '') {
            return 0.0;
        }

        $value = preg_replace('/[^0-9,.-]/', '', $value) ?? '';
        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
            $value = str_replace('.', '', $value);
        }

        return max(0, (float)$value);
    }

    private function uploadedReceiptDocument(string $fieldName, ?int $index = null): ?array
    {
        if (!$this->receipts->hasDocumentColumns() || empty($_FILES[$fieldName])) {
            return null;
        }

        $file = $_FILES[$fieldName];
        $error = $index === null ? ($file['error'] ?? UPLOAD_ERR_NO_FILE) : ($file['error'][$index] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo subir el documento tributario.');
        }

        $tmpName = $index === null ? ($file['tmp_name'] ?? '') : ($file['tmp_name'][$index] ?? '');
        $originalName = $index === null ? ($file['name'] ?? '') : ($file['name'][$index] ?? '');
        $size = (int)($index === null ? ($file['size'] ?? 0) : ($file['size'][$index] ?? 0));

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new RuntimeException('El archivo recibido no es válido.');
        }
        if ($size > 10 * 1024 * 1024) {
            throw new RuntimeException('El documento no puede superar los 10 MB.');
        }

        $mimeType = mime_content_type($tmpName) ?: '';
        $allowed = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mimeType])) {
            throw new RuntimeException('Solo se permiten documentos PDF o imágenes JPG, PNG y WEBP.');
        }

        $companyId = $this->requireCompany();
        $relativeDir = 'storage/uploads/petty-cash/' . $companyId . '/' . date('Y/m');
        $absoluteDir = dirname(__DIR__, 2) . '/' . $relativeDir;
        if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0775, true) && !is_dir($absoluteDir)) {
            throw new RuntimeException('No se pudo preparar la carpeta de documentos.');
        }

        $fileName = 'boleta_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mimeType];
        $relativePath = $relativeDir . '/' . $fileName;
        if (!move_uploaded_file($tmpName, dirname(__DIR__, 2) . '/' . $relativePath)) {
            throw new RuntimeException('No se pudo guardar el documento tributario.');
        }

        return [
            'document_path' => $relativePath,
            'document_original_name' => substr($originalName, 0, 255),
            'document_mime_type' => $mimeType,
            'document_size' => $size,
        ];
    }

    private function createReceiptWithItems(array $receiptData, array $rows): int
    {
        $receiptId = $this->receipts->create($receiptData);

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
        return $receiptId;
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
        $isQuickTable = ($_POST['entry_mode'] ?? '') === 'quick_table';

        if ($isQuickTable) {
            $dates = $_POST['quick_receipt_date'] ?? [];
            $numbers = $_POST['quick_receipt_number'] ?? [];
            $details = $_POST['quick_description'] ?? [];
            $amounts = $_POST['quick_amount'] ?? [];
            $suppliers = $_POST['quick_supplier_name'] ?? [];
            $notes = trim($_POST['quick_notes'] ?? '');
            $currency = trim($_POST['quick_currency'] ?? 'CLP');

            $receipts = [];
            foreach ($details as $index => $detail) {
                $detail = trim((string)$detail);
                $amount = $this->normalizeAmount($amounts[$index] ?? 0);
                $number = trim((string)($numbers[$index] ?? ''));
                $date = trim((string)($dates[$index] ?? date('Y-m-d')));
                $supplier = trim((string)($suppliers[$index] ?? ''));

                if ($detail === '' && $number === '' && $amount <= 0) {
                    continue;
                }
                if ($detail === '' || $number === '' || $amount <= 0) {
                    flash('error', 'Cada fila rápida debe tener fecha, N° boleta, detalle y valor.');
                    $this->redirect('index.php?route=petty-cash/create');
                }

                $document = null;
                try {
                    $document = $this->uploadedReceiptDocument('quick_document', (int)$index);
                } catch (Throwable $e) {
                    flash('error', $e->getMessage());
                    $this->redirect('index.php?route=petty-cash/create');
                }

                $receipts[] = [
                    'data' => array_merge([
                        'company_id' => $companyId,
                        'receipt_number' => $number,
                        'receipt_date' => $date !== '' ? $date : date('Y-m-d'),
                        'supplier_name' => $supplier !== '' ? $supplier : 'Caja chica',
                        'currency' => $currency !== '' ? $currency : 'CLP',
                        'total_amount' => $amount,
                        'notes' => $notes,
                        'created_by' => (int)(Auth::user()['id'] ?? 0),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], $document ?? []),
                    'rows' => [[
                        'product_id' => null,
                        'description' => $detail,
                        'quantity' => 1,
                        'unit_price' => $amount,
                        'subtotal' => $amount,
                        'observation' => '',
                    ]],
                ];
            }

            if (empty($receipts)) {
                flash('error', 'Debes ingresar al menos una fila rápida válida.');
                $this->redirect('index.php?route=petty-cash/create');
            }

            $pdo = $this->db->pdo();
            try {
                $pdo->beginTransaction();
                foreach ($receipts as $receipt) {
                    $this->createReceiptWithItems($receipt['data'], $receipt['rows']);
                }
                $pdo->commit();
                flash('success', count($receipts) . ' registro(s) de caja chica guardado(s) correctamente.');
            } catch (Throwable $e) {
                $pdo->rollBack();
                log_message('error', 'Error guardando caja chica rápida: ' . $e->getMessage());
                flash('error', 'No se pudieron guardar los registros rápidos.');
            }

            $this->redirect('index.php?route=petty-cash');
        }

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
            $qty = max(0, $this->normalizeAmount($itemQuantities[$index] ?? 0));
            $price = max(0, $this->normalizeAmount($itemPrices[$index] ?? 0));
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

        try {
            $documentData = $this->uploadedReceiptDocument('document_file');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            $this->redirect('index.php?route=petty-cash/create');
        }

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();

            $receiptData = array_merge([
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
            ], $documentData ?? []);

            $receiptId = $this->createReceiptWithItems($receiptData, $rows);
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
        $unitMeasure = trim($_POST['unit_measure'] ?? 'Unidad');
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
            $productData = [
                'company_id' => $companyId,
                'name' => $name,
                'classification' => $classification,
                'category' => $category,
                'suggested_price' => $price,
                'unit_measure' => $unitMeasure,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($this->products->hasUnitMeasureColumn()) {
                $productData['unit_measure'] = $unitMeasure !== '' ? $unitMeasure : 'Unidad';
            }

            $this->products->create($productData);
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


    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $receiptId = (int)($_GET['id'] ?? 0);
        $receipt = $this->receipts->findForCompany($receiptId, $companyId);

        if (!$receipt) {
            flash('error', 'Boleta no encontrada.');
            $this->redirect('index.php?route=petty-cash');
        }

        $this->render('petty-cash/edit', [
            'title' => 'Caja chica',
            'pageTitle' => 'Editar boleta de caja chica',
            'products' => $this->products->active($companyId),
            'receipt' => $receipt,
            'items' => $this->items->byReceipt($receiptId),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $receiptId = (int)($_POST['id'] ?? 0);
        $receipt = $this->receipts->findForCompany($receiptId, $companyId);

        if (!$receipt) {
            flash('error', 'Boleta no encontrada.');
            $this->redirect('index.php?route=petty-cash');
        }

        $receiptNumber = trim($_POST['receipt_number'] ?? '');
        $receiptDate = trim($_POST['receipt_date'] ?? date('Y-m-d'));
        $supplierName = trim($_POST['supplier_name'] ?? '');
        $currency = trim($_POST['currency'] ?? 'CLP');
        $notes = trim($_POST['notes'] ?? '');

        if ($receiptNumber === '' || $supplierName === '') {
            flash('error', 'Debes completar número de boleta y proveedor.');
            $this->redirect('index.php?route=petty-cash/edit&id=' . $receiptId);
        }

        $itemProductIds = $_POST['item_product_id'] ?? [];
        $itemDescriptions = $_POST['item_description'] ?? [];
        $itemQuantities = $_POST['item_quantity'] ?? [];
        $itemPrices = $_POST['item_unit_price'] ?? [];
        $itemObservations = $_POST['item_observation'] ?? [];

        $rows = [];
        foreach ($itemDescriptions as $index => $description) {
            $description = trim((string)$description);
            $qty = max(0, $this->normalizeAmount($itemQuantities[$index] ?? 0));
            $price = max(0, $this->normalizeAmount($itemPrices[$index] ?? 0));
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
            $this->redirect('index.php?route=petty-cash/edit&id=' . $receiptId);
        }

        $total = array_sum(array_map(static fn(array $r): float => (float)$r['subtotal'], $rows));

        try {
            $documentData = $this->uploadedReceiptDocument('document_file');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            $this->redirect('index.php?route=petty-cash/edit&id=' . $receiptId);
        }

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();

            $receiptData = array_merge([
                'receipt_number' => $receiptNumber,
                'receipt_date' => $receiptDate,
                'supplier_name' => $supplierName,
                'currency' => $currency,
                'total_amount' => $total,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s'),
            ], $documentData ?? []);

            $this->receipts->update($receiptId, $receiptData);

            $this->items->deleteByReceipt($receiptId);
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

            audit($this->db, Auth::user()['id'] ?? null, 'update', 'petty_cash_receipts', $receiptId);
            $pdo->commit();
            flash('success', 'Boleta actualizada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error actualizando caja chica: ' . $e->getMessage());
            flash('error', 'No se pudo actualizar la boleta.');
        }

        $this->redirect('index.php?route=petty-cash');
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
