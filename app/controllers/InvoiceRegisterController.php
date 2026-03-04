<?php

class InvoiceRegisterController extends Controller
{
    private InvoiceRegisterModel $records;
    private InvoiceRegisterItemsModel $items;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->records = new InvoiceRegisterModel($db);
        $this->items = new InvoiceRegisterItemsModel($db);
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
        $this->requireCompany();

        $this->render('invoice-register/create', [
            'title' => 'Registro facturas',
            'pageTitle' => 'Registrar factura de compra o servicio',
            'today' => date('Y-m-d'),
        ]);
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
