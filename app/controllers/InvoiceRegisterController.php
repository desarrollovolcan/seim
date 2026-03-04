<?php

class InvoiceRegisterController extends Controller
{
    private InvoiceRegisterModel $records;
    private InvoiceRegisterItemsModel $items;
    private SuppliersModel $suppliers;
    private PettyCashProductsModel $catalogProducts;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->records = new InvoiceRegisterModel($db);
        $this->items = new InvoiceRegisterItemsModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->catalogProducts = new PettyCashProductsModel($db);
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
        $suppliers = $this->suppliers->active($companyId);
        $catalogProducts = $this->catalogProducts->active($companyId);

        $this->render('invoice-register/create', [
            'title' => 'Registro facturas',
            'pageTitle' => 'Registrar factura de compra o servicio',
            'today' => date('Y-m-d'),
            'suppliers' => $suppliers,
            'catalogProducts' => $catalogProducts,
        ]);
    }

    public function storeQuickSupplier(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $isAjax = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

        $name = trim($_POST['supplier_name'] ?? '');
        $code = trim($_POST['supplier_code'] ?? '');
        $email = trim($_POST['supplier_email'] ?? '');
        $website = trim($_POST['supplier_website'] ?? '');

        if (!Validator::required($name) || !Validator::required($code)) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'Nombre y código del proveedor son obligatorios.']);
                exit;
            }

            flash('error', 'Nombre y código del proveedor son obligatorios.');
            $this->redirect('index.php?route=invoice-register/create');
        }

        if (!Validator::optionalEmail($email)) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'El email del proveedor no es válido.']);
                exit;
            }

            flash('error', 'El email del proveedor no es válido.');
            $this->redirect('index.php?route=invoice-register/create');
        }

        if (!Validator::url($website)) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'El sitio web del proveedor no es válido.']);
                exit;
            }

            flash('error', 'El sitio web del proveedor no es válido.');
            $this->redirect('index.php?route=invoice-register/create');
        }

        try {
            $supplierId = $this->suppliers->create([
                'company_id' => $companyId,
                'name' => $name,
                'code' => $code,
                'contact_name' => trim($_POST['supplier_contact_name'] ?? ''),
                'tax_id' => trim($_POST['supplier_tax_id'] ?? ''),
                'email' => $email,
                'phone' => trim($_POST['supplier_phone'] ?? ''),
                'address' => trim($_POST['supplier_address'] ?? ''),
                'giro' => trim($_POST['supplier_giro'] ?? ''),
                'commune' => trim($_POST['supplier_commune'] ?? ''),
                'website' => $website,
                'notes' => trim($_POST['supplier_notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => true,
                    'supplier' => [
                        'id' => $supplierId,
                        'name' => $name,
                        'tax_id' => trim($_POST['supplier_tax_id'] ?? ''),
                    ],
                ]);
                exit;
            }

            flash('success', 'Proveedor creado correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Error creando proveedor rápido en registro facturas: ' . $e->getMessage());

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode(['ok' => false, 'message' => 'No se pudo crear el proveedor.']);
                exit;
            }

            flash('error', 'No se pudo crear el proveedor.');
        }

        $this->redirect('index.php?route=invoice-register/create');
    }

    public function storeCatalogProduct(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $isAjax = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

        $name = trim($_POST['name'] ?? '');
        $classification = trim($_POST['classification'] ?? 'servicio');
        $category = trim($_POST['category'] ?? 'General');
        $unitMeasure = trim($_POST['unit_measure'] ?? 'Unidad');
        $suggestedPrice = max(0, (float)($_POST['suggested_price'] ?? 0));

        if (!in_array($classification, ['producto', 'servicio'], true)) {
            $classification = 'servicio';
        }

        if ($name === '') {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'Debes ingresar el nombre del ítem.']);
                exit;
            }

            flash('error', 'Debes ingresar el nombre del ítem.');
            $this->redirect('index.php?route=invoice-register/create');
        }

        try {
            $catalogData = [
                'company_id' => $companyId,
                'name' => $name,
                'classification' => $classification,
                'category' => $category,
                'suggested_price' => $suggestedPrice,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->catalogProducts->hasUnitMeasureColumn()) {
                $catalogData['unit_measure'] = $unitMeasure !== '' ? $unitMeasure : 'Unidad';
            }

            $catalogId = $this->catalogProducts->create($catalogData);

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => true,
                    'product' => [
                        'id' => $catalogId,
                        'name' => $name,
                        'classification' => $classification,
                        'suggested_price' => $suggestedPrice,
                    ],
                ]);
                exit;
            }

            flash('success', 'Ítem agregado al catálogo compartido correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Error al crear ítem para registro facturas: ' . $e->getMessage());

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode(['ok' => false, 'message' => 'No se pudo guardar el ítem en el catálogo.']);
                exit;
            }

            flash('error', 'No se pudo guardar el ítem en el catálogo.');
        }

        $this->redirect('index.php?route=invoice-register/create');
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $documentType = trim($_POST['document_type'] ?? 'factura');
        $invoiceNumber = trim($_POST['invoice_number'] ?? '');
        $invoiceDate = trim($_POST['invoice_date'] ?? date('Y-m-d'));
        $dueDate = trim($_POST['due_date'] ?? '');
        $supplierName = trim($_POST['supplier_name'] ?? '');
        $supplierTaxId = trim($_POST['supplier_tax_id'] ?? '');
        $currency = trim($_POST['currency'] ?? 'CLP');
        $notes = trim($_POST['notes'] ?? '');

        if ($invoiceNumber === '' || $supplierName === '') {
            flash('error', 'Debes completar número de factura y proveedor.');
            $this->redirect('index.php?route=invoice-register/create');
        }

        $itemTypes = $_POST['item_type'] ?? [];
        $itemDescriptions = $_POST['item_description'] ?? [];
        $itemQuantities = $_POST['item_quantity'] ?? [];
        $itemPrices = $_POST['item_unit_price'] ?? [];
        $itemObservations = $_POST['item_observation'] ?? [];

        $rows = [];
        foreach ($itemDescriptions as $index => $description) {
            $description = trim((string)$description);
            $qty = max(0, (float)($itemQuantities[$index] ?? 0));
            $price = max(0, (float)($itemPrices[$index] ?? 0));
            $type = trim((string)($itemTypes[$index] ?? 'producto'));
            $observation = trim((string)($itemObservations[$index] ?? ''));

            if (!in_array($type, ['producto', 'servicio'], true)) {
                $type = 'producto';
            }

            if ($description === '' || $qty <= 0) {
                continue;
            }

            $rows[] = [
                'item_type' => $type,
                'description' => $description,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => round($qty * $price, 2),
                'observation' => $observation,
            ];
        }

        if (empty($rows)) {
            flash('error', 'Debes ingresar al menos un producto/servicio válido.');
            $this->redirect('index.php?route=invoice-register/create');
        }

        $netAmount = array_sum(array_map(static fn(array $r): float => (float)$r['subtotal'], $rows));
        $taxAmount = round($netAmount * 0.19, 2);
        $totalAmount = $netAmount + $taxAmount;

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();

            $recordId = $this->records->create([
                'company_id' => $companyId,
                'document_type' => $documentType,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate !== '' ? $dueDate : null,
                'supplier_name' => $supplierName,
                'supplier_tax_id' => $supplierTaxId,
                'currency' => $currency,
                'net_amount' => $netAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $notes,
                'created_by' => (int)(Auth::user()['id'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($rows as $row) {
                $this->items->create([
                    'invoice_id' => $recordId,
                    'item_type' => $row['item_type'],
                    'description' => $row['description'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'subtotal' => $row['subtotal'],
                    'observation' => $row['observation'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            audit($this->db, Auth::user()['id'] ?? null, 'create', 'purchase_invoice_records', $recordId);
            $pdo->commit();
            flash('success', 'Factura registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error guardando registro facturas: ' . $e->getMessage());
            flash('error', 'No se pudo registrar la factura.');
        }

        $this->redirect('index.php?route=invoice-register');
    }


    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $recordId = (int)($_GET['id'] ?? 0);
        $record = $this->records->findForCompany($recordId, $companyId);

        if (!$record) {
            flash('error', 'Factura no encontrada.');
            $this->redirect('index.php?route=invoice-register');
        }

        $this->render('invoice-register/edit', [
            'title' => 'Registro facturas',
            'pageTitle' => 'Editar factura de compra o servicio',
            'record' => $record,
            'items' => $this->items->byInvoice($recordId),
            'suppliers' => $this->suppliers->active($companyId),
            'catalogProducts' => $this->catalogProducts->active($companyId),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $recordId = (int)($_POST['id'] ?? 0);
        $record = $this->records->findForCompany($recordId, $companyId);

        if (!$record) {
            flash('error', 'Factura no encontrada.');
            $this->redirect('index.php?route=invoice-register');
        }

        $documentType = trim($_POST['document_type'] ?? 'factura');
        $invoiceNumber = trim($_POST['invoice_number'] ?? '');
        $invoiceDate = trim($_POST['invoice_date'] ?? date('Y-m-d'));
        $dueDate = trim($_POST['due_date'] ?? '');
        $supplierName = trim($_POST['supplier_name'] ?? '');
        $supplierTaxId = trim($_POST['supplier_tax_id'] ?? '');
        $currency = trim($_POST['currency'] ?? 'CLP');
        $notes = trim($_POST['notes'] ?? '');

        if ($invoiceNumber === '' || $supplierName === '') {
            flash('error', 'Debes completar número de factura y proveedor.');
            $this->redirect('index.php?route=invoice-register/edit&id=' . $recordId);
        }

        $itemTypes = $_POST['item_type'] ?? [];
        $itemDescriptions = $_POST['item_description'] ?? [];
        $itemQuantities = $_POST['item_quantity'] ?? [];
        $itemPrices = $_POST['item_unit_price'] ?? [];
        $itemObservations = $_POST['item_observation'] ?? [];

        $rows = [];
        foreach ($itemDescriptions as $index => $description) {
            $description = trim((string)$description);
            $qty = max(0, (float)($itemQuantities[$index] ?? 0));
            $price = max(0, (float)($itemPrices[$index] ?? 0));
            $type = trim((string)($itemTypes[$index] ?? 'producto'));
            $observation = trim((string)($itemObservations[$index] ?? ''));

            if (!in_array($type, ['producto', 'servicio'], true)) {
                $type = 'producto';
            }

            if ($description === '' || $qty <= 0) {
                continue;
            }

            $rows[] = [
                'item_type' => $type,
                'description' => $description,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => round($qty * $price, 2),
                'observation' => $observation,
            ];
        }

        if (empty($rows)) {
            flash('error', 'Debes ingresar al menos un producto/servicio válido.');
            $this->redirect('index.php?route=invoice-register/edit&id=' . $recordId);
        }

        $netAmount = array_sum(array_map(static fn(array $r): float => (float)$r['subtotal'], $rows));
        $taxAmount = round($netAmount * 0.19, 2);
        $totalAmount = $netAmount + $taxAmount;

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();

            $this->records->update($recordId, [
                'document_type' => $documentType,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate !== '' ? $dueDate : null,
                'supplier_name' => $supplierName,
                'supplier_tax_id' => $supplierTaxId,
                'currency' => $currency,
                'net_amount' => $netAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->items->deleteByInvoice($recordId);
            foreach ($rows as $row) {
                $this->items->create([
                    'invoice_id' => $recordId,
                    'item_type' => $row['item_type'],
                    'description' => $row['description'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'subtotal' => $row['subtotal'],
                    'observation' => $row['observation'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            audit($this->db, Auth::user()['id'] ?? null, 'update', 'purchase_invoice_records', $recordId);
            $pdo->commit();
            flash('success', 'Factura actualizada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error actualizando registro facturas: ' . $e->getMessage());
            flash('error', 'No se pudo actualizar la factura.');
        }

        $this->redirect('index.php?route=invoice-register');
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $filters = [
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to' => trim($_GET['date_to'] ?? ''),
            'supplier' => trim($_GET['supplier'] ?? ''),
            'invoice_number' => trim($_GET['invoice_number'] ?? ''),
        ];

        $records = $this->records->listWithFilters($companyId, $filters);
        foreach ($records as &$record) {
            $record['items'] = $this->items->byInvoice((int)$record['id']);
        }
        unset($record);

        $this->render('invoice-register/index', [
            'title' => 'Registro facturas',
            'pageTitle' => 'Listado de facturas de compra/servicios',
            'filters' => $filters,
            'records' => $records,
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
            'invoice_number' => trim($_GET['invoice_number'] ?? ''),
        ];

        $records = $this->records->listWithFilters($companyId, $filters);

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="registro_facturas_' . date('Ymd_His') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<html><head><meta charset="UTF-8">';
        echo '<style>';
        echo 'table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;width:100%;}';
        echo 'th,td{border:1px solid #777;padding:6px 8px;}';
        echo 'th{background:#e6e6e6;font-weight:bold;text-align:center;}';
        echo '.text-right{text-align:right;}';
        echo '.text-center{text-align:center;}';
        echo '</style>';
        echo '</head><body>';

        echo '<table>';
        echo '<thead><tr>';
        echo '<th>N°</th><th>Fecha</th><th>Tipo Doc.</th><th>N° Factura</th><th>Proveedor</th><th>RUT</th><th>Tipo ítem</th><th>Detalle</th><th>Cant.</th><th>Valor Unit.</th><th>Subtotal</th><th>Obs.</th>';
        echo '</tr></thead><tbody>';

        $rowNumber = 1;
        foreach ($records as $record) {
            $items = $this->items->byInvoice((int)$record['id']);
            if (empty($items)) {
                $items = [[
                    'item_type' => '-',
                    'description' => '-',
                    'quantity' => 0,
                    'unit_price' => 0,
                    'subtotal' => 0,
                    'observation' => '',
                ]];
            }

            foreach ($items as $item) {
                echo '<tr>';
                echo '<td class="text-center">' . $rowNumber . '</td>';
                echo '<td>' . e(date('d.m.Y', strtotime((string)$record['invoice_date']))) . '</td>';
                echo '<td>' . e(ucfirst((string)$record['document_type'])) . '</td>';
                echo '<td>' . e((string)$record['invoice_number']) . '</td>';
                echo '<td>' . e((string)$record['supplier_name']) . '</td>';
                echo '<td>' . e((string)($record['supplier_tax_id'] ?? '')) . '</td>';
                echo '<td>' . e((string)$item['item_type']) . '</td>';
                echo '<td>' . e((string)$item['description']) . '</td>';
                echo '<td class="text-right">' . number_format((float)$item['quantity'], 2, ',', '.') . '</td>';
                echo '<td class="text-right">$ ' . number_format((float)$item['unit_price'], 0, ',', '.') . '</td>';
                echo '<td class="text-right">$ ' . number_format((float)$item['subtotal'], 0, ',', '.') . '</td>';
                echo '<td>' . e((string)($item['observation'] ?? '')) . '</td>';
                echo '</tr>';
                $rowNumber++;
            }
        }

        echo '</tbody></table></body></html>';
        exit;
    }
}
