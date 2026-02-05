<?php

class QuotesController extends Controller
{
    private QuotesModel $quotes;
    private ClientsModel $clients;
    private SystemServicesModel $services;
    private ProducedProductsModel $producedProducts;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->quotes = new QuotesModel($db);
        $this->clients = new ClientsModel($db);
        $this->services = new SystemServicesModel($db);
        $this->producedProducts = new ProducedProductsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $quotes = $this->quotes->allWithClient($companyId);
        $this->render('quotes/index', [
            'title' => 'Cotizaciones',
            'pageTitle' => 'Cotizaciones',
            'quotes' => $quotes,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $clients = $this->clients->active($companyId);
        $products = $this->db->fetchAll(
            'SELECT p.id, p.name, p.price, p.stock
             FROM products p
             WHERE p.company_id = :company_id AND p.deleted_at IS NULL
             ORDER BY p.name ASC',
            ['company_id' => $companyId]
        );
        $producedProducts = $this->producedProducts->active($companyId);
        $number = $this->quotes->nextNumber('COT-', $companyId);
        $selectedClientId = (int)($_GET['client_id'] ?? 0);
        $this->render('quotes/create', [
            'title' => 'Nueva Cotización',
            'pageTitle' => 'Nueva Cotización',
            'clients' => $clients,
            'products' => $products,
            'producedProducts' => $producedProducts,
            'number' => $number,
            'selectedClientId' => $selectedClientId,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $serviceId = trim($_POST['system_service_id'] ?? '');
        $projectId = trim($_POST['project_id'] ?? '');
        $issueDate = trim($_POST['fecha_emision'] ?? '');
        $discountTotal = (float)($_POST['discount_total'] ?? 0);
        $discountTotalType = $_POST['discount_total_type'] ?? 'amount';
        $numero = trim($_POST['numero'] ?? '');
        if ($numero === '') {
            $numero = $this->quotes->nextNumber('COT-', $companyId);
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id, rut, name, giro, address, commune FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=quotes/create');
        }
        $siiData = sii_document_payload($_POST, sii_receiver_payload($client));
        $siiErrors = validate_sii_document_payload($siiData);
        if ($siiErrors) {
            flash('error', implode(' ', $siiErrors));
            $this->redirect('index.php?route=quotes/create');
        }
        if ($serviceId !== '') {
            $service = $this->db->fetch(
                'SELECT id FROM system_services WHERE id = :id AND company_id = :company_id',
                ['id' => $serviceId, 'company_id' => $companyId]
            );
            if (!$service) {
                flash('error', 'Servicio no encontrado para esta empresa.');
                $this->redirect('index.php?route=quotes/create');
            }
        }

        $items = $_POST['items'] ?? [];
        $normalizedItems = [];
        $subtotal = 0.0;
        foreach ($items as $item) {
            if (empty($item['descripcion'])) {
                continue;
            }
            $qty = max(1, (int)($item['cantidad'] ?? 1));
            $price = max(0.0, (float)($item['precio_unitario'] ?? 0));
            $discountValue = max(0.0, (float)($item['descuento'] ?? 0));
            $discountType = $item['discount_type'] ?? 'amount';
            $lineBase = $qty * $price;
            $discountAmount = $discountType === 'percent'
                ? ($lineBase * $discountValue / 100)
                : $discountValue;
            $discountAmount = min($lineBase, max(0.0, $discountAmount));
            $lineTotal = max(0.0, $lineBase - $discountAmount);
            $subtotal += $lineTotal;
            $normalizedItems[] = [
                'descripcion' => $item['descripcion'],
                'cantidad' => $qty,
                'precio_unitario' => $price,
                'descuento' => $discountValue,
                'discount_type' => $discountType,
                'total' => $lineTotal,
            ];
        }
        if (empty($normalizedItems)) {
            flash('error', 'Agrega al menos un ítem a la cotización.');
            $this->redirect('index.php?route=quotes/create');
        }

        $applyTax = ($_POST['apply_tax_display'] ?? '1') === '1';
        $taxRate = (float)($_POST['tax_rate'] ?? 0);
        $discountTotal = max(0.0, $discountTotal);
        $discountTotalType = $discountTotalType === 'percent' ? 'percent' : 'amount';
        $discountTotalAmount = $discountTotalType === 'percent'
            ? ($subtotal * $discountTotal / 100)
            : $discountTotal;
        $discountTotalAmount = min($subtotal, max(0.0, $discountTotalAmount));
        $taxableBase = max(0.0, $subtotal - $discountTotalAmount);
        $impuestos = $applyTax ? round($taxableBase * ($taxRate / 100), 2) : 0.0;
        $total = $taxableBase + $impuestos;

        $quoteId = $this->quotes->create(array_merge([
            'company_id' => $companyId,
            'client_id' => $clientId,
            'system_service_id' => $serviceId !== '' ? $serviceId : null,
            'project_id' => $projectId !== '' ? $projectId : null,
            'numero' => $numero,
            'fecha_emision' => $issueDate !== '' ? $issueDate : date('Y-m-d'),
            'estado' => $_POST['estado'] ?? 'pendiente',
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'discount_total_type' => $discountTotalType,
            'impuestos' => $impuestos,
            'total' => $total,
            'notas' => trim($_POST['notas'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $siiData));

        $itemsModel = new QuoteItemsModel($this->db);
        foreach ($normalizedItems as $item) {
            $itemsModel->create([
                'quote_id' => $quoteId,
                'descripcion' => $item['descripcion'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'descuento' => $item['descuento'],
                'discount_type' => $item['discount_type'],
                'total' => $item['total'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        create_notification(
            $this->db,
            $companyId,
            'Nueva cotización',
            'Se creó la cotización ' . ($numero !== '' ? $numero : '#' . $quoteId) . '.',
            'success'
        );
        audit($this->db, Auth::user()['id'], 'create', 'quotes', $quoteId);
        flash('success', 'Cotización creada correctamente.');
        $this->redirect('index.php?route=quotes');
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_GET['id'] ?? 0);
        $quote = $this->db->fetch(
            'SELECT * FROM quotes WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$quote) {
            $this->redirect('index.php?route=quotes');
        }
        $items = (new QuoteItemsModel($this->db))->byQuote($id);
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $quote['client_id'], 'company_id' => $companyId]
        );
        $this->render('quotes/show', [
            'title' => 'Detalle Cotización',
            'pageTitle' => 'Detalle Cotización',
            'quote' => $quote,
            'client' => $client,
            'items' => $items,
        ]);
    }

    public function edit(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $quote = $this->db->fetch(
            'SELECT * FROM quotes WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$quote) {
            $this->redirect('index.php?route=quotes');
        }
        $items = (new QuoteItemsModel($this->db))->byQuote($id);
        $clients = $this->clients->active($companyId);
        $services = $this->services->allWithType($companyId);
        $products = $this->db->fetchAll(
            'SELECT p.id, p.name, p.price, p.stock
             FROM products p
             WHERE p.company_id = :company_id AND p.deleted_at IS NULL
             ORDER BY p.name ASC',
            ['company_id' => $companyId]
        );
        $producedProducts = $this->producedProducts->active($companyId);
        $projects = $this->db->fetchAll(
            'SELECT projects.*, clients.name as client_name FROM projects JOIN clients ON projects.client_id = clients.id WHERE projects.deleted_at IS NULL AND projects.company_id = :company_id ORDER BY projects.id DESC',
            ['company_id' => $companyId]
        );
        $this->render('quotes/edit', [
            'title' => 'Editar Cotización',
            'pageTitle' => 'Editar Cotización',
            'quote' => $quote,
            'items' => $items,
            'clients' => $clients,
            'services' => $services,
            'products' => $products,
            'producedProducts' => $producedProducts,
            'projects' => $projects,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $quote = $this->db->fetch(
            'SELECT * FROM quotes WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$quote) {
            $this->redirect('index.php?route=quotes');
        }
        $serviceId = trim($_POST['system_service_id'] ?? '');
        $projectId = trim($_POST['project_id'] ?? '');
        $issueDate = trim($_POST['fecha_emision'] ?? '');
        $discountTotal = (float)($_POST['discount_total'] ?? 0);

        if ($serviceId !== '') {
            $service = $this->db->fetch(
                'SELECT id FROM system_services WHERE id = :id AND company_id = :company_id',
                ['id' => $serviceId, 'company_id' => $companyId]
            );
            if (!$service) {
                flash('error', 'Servicio no encontrado para esta empresa.');
                $this->redirect('index.php?route=quotes/edit&id=' . $id);
            }
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id, rut, name, giro, address, commune FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=quotes/edit&id=' . $id);
        }
        $siiData = sii_document_payload($_POST, sii_receiver_payload($client));
        $siiErrors = validate_sii_document_payload($siiData);
        if ($siiErrors) {
            flash('error', implode(' ', $siiErrors));
            $this->redirect('index.php?route=quotes/edit&id=' . $id);
        }
        $items = $_POST['items'] ?? [];
        $normalizedItems = [];
        $subtotal = 0.0;
        foreach ($items as $item) {
            if (empty($item['descripcion'])) {
                continue;
            }
            $qty = max(1, (int)($item['cantidad'] ?? 1));
            $price = max(0.0, (float)($item['precio_unitario'] ?? 0));
            $discountValue = max(0.0, (float)($item['descuento'] ?? 0));
            $discountType = $item['discount_type'] ?? 'amount';
            $lineBase = $qty * $price;
            $discountAmount = $discountType === 'percent'
                ? ($lineBase * $discountValue / 100)
                : $discountValue;
            $discountAmount = min($lineBase, max(0.0, $discountAmount));
            $lineTotal = max(0.0, $lineBase - $discountAmount);
            $subtotal += $lineTotal;
            $normalizedItems[] = [
                'descripcion' => $item['descripcion'],
                'cantidad' => $qty,
                'precio_unitario' => $price,
                'descuento' => $discountValue,
                'discount_type' => $discountType,
                'total' => $lineTotal,
            ];
        }
        if (empty($normalizedItems)) {
            flash('error', 'Agrega al menos un ítem a la cotización.');
            $this->redirect('index.php?route=quotes/edit&id=' . $id);
        }

        $applyTax = ($_POST['apply_tax_display'] ?? '1') === '1';
        $taxRate = (float)($_POST['tax_rate'] ?? 0);
        $discountTotal = max(0.0, $discountTotal);
        $discountTotalType = $discountTotalType === 'percent' ? 'percent' : 'amount';
        $discountTotalAmount = $discountTotalType === 'percent'
            ? ($subtotal * $discountTotal / 100)
            : $discountTotal;
        $discountTotalAmount = min($subtotal, max(0.0, $discountTotalAmount));
        $taxableBase = max(0.0, $subtotal - $discountTotalAmount);
        $impuestos = $applyTax ? round($taxableBase * ($taxRate / 100), 2) : 0.0;
        $total = $taxableBase + $impuestos;

        $this->quotes->update($id, array_merge([
            'client_id' => $clientId,
            'system_service_id' => $serviceId !== '' ? $serviceId : null,
            'project_id' => $projectId !== '' ? $projectId : null,
            'numero' => trim($_POST['numero'] ?? ''),
            'fecha_emision' => $issueDate !== '' ? $issueDate : $quote['fecha_emision'],
            'estado' => $_POST['estado'] ?? 'pendiente',
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'discount_total_type' => $discountTotalType,
            'impuestos' => $impuestos,
            'total' => $total,
            'notas' => trim($_POST['notas'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $siiData));

        $this->db->execute('DELETE FROM quote_items WHERE quote_id = :quote_id', ['quote_id' => $id]);
        $itemsModel = new QuoteItemsModel($this->db);
        foreach ($normalizedItems as $item) {
            $itemsModel->create([
                'quote_id' => $id,
                'descripcion' => $item['descripcion'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'descuento' => $item['descuento'],
                'discount_type' => $item['discount_type'],
                'total' => $item['total'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        audit($this->db, Auth::user()['id'], 'update', 'quotes', $id);
        flash('success', 'Cotización actualizada correctamente.');
        $this->redirect('index.php?route=quotes/show&id=' . $id);
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $quote = $this->db->fetch(
            'SELECT id FROM quotes WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$quote) {
            flash('error', 'Cotización no encontrada para esta empresa.');
            $this->redirect('index.php?route=quotes');
        }
        $this->db->execute('DELETE FROM quote_items WHERE quote_id = :quote_id', ['quote_id' => $id]);
        $this->db->execute('DELETE FROM quotes WHERE id = :id', ['id' => $id]);
        audit($this->db, Auth::user()['id'], 'delete', 'quotes', $id);
        flash('success', 'Cotización eliminada correctamente.');
        $this->redirect('index.php?route=quotes');
    }

    public function send(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $quote = $this->db->fetch(
            'SELECT * FROM quotes WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$quote) {
            $this->redirect('index.php?route=quotes');
        }
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $quote['client_id'], 'company_id' => $companyId]
        );
        $recipient = $client['email'] ?? '';
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->db->execute('INSERT INTO notifications (company_id, title, message, type, created_at, updated_at) VALUES (:company_id, :title, :message, :type, NOW(), NOW())', [
                'company_id' => current_company_id(),
                'title' => 'Cotización no enviada',
                'message' => 'El cliente no tiene un correo válido.',
                'type' => 'danger',
            ]);
            flash('error', 'No se pudo enviar la cotización: email inválido.');
            $this->redirect('index.php?route=quotes');
        }
        $baseUrl = rtrim($this->config['app']['base_url'] ?? '', '/');
        if ($baseUrl === '') {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $scheme . '://' . $host;
        }
        $printUrl = $baseUrl . '/index.php?route=quotes/print&id=' . $id;
        $subject = 'Cotización ' . ($quote['numero'] ?? '');
        $body = '<p>Adjuntamos la cotización solicitada.</p>'
            . '<p><a href="' . e($printUrl) . '">Ver cotización</a></p>';
        $sent = (new Mailer($this->db))->send('info', $recipient, $subject, $body);
        $this->db->execute('INSERT INTO notifications (company_id, title, message, type, created_at, updated_at) VALUES (:company_id, :title, :message, :type, NOW(), NOW())', [
            'company_id' => current_company_id(),
            'title' => $sent ? 'Cotización enviada' : 'Cotización no enviada',
            'message' => $sent ? 'La cotización fue enviada correctamente.' : 'No se pudo enviar la cotización.',
            'type' => $sent ? 'success' : 'danger',
        ]);
        flash($sent ? 'success' : 'error', $sent ? 'Cotización enviada correctamente.' : 'No se pudo enviar la cotización.');
        $this->redirect('index.php?route=quotes');
    }

    public function print(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $quote = $this->db->fetch(
            'SELECT * FROM quotes WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$quote) {
            $this->redirect('index.php?route=quotes');
        }
        $items = (new QuoteItemsModel($this->db))->byQuote($id);
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $quote['client_id'], 'company_id' => current_company_id()]
        );
        $company = (new SettingsModel($this->db))->get('company', []);
        $currentUser = Auth::user();
        $sellerUser = null;
        if (!empty($currentUser['id'])) {
            $sellerUser = $this->db->fetch(
                'SELECT id, name, signature, signature_image_path FROM users WHERE id = :id AND deleted_at IS NULL',
                ['id' => (int)$currentUser['id']]
            );
        }
        $viewPath = __DIR__ . '/../views/quotes/print.php';
        if (file_exists($viewPath)) {
            include $viewPath;
            return;
        }
        echo 'Vista no encontrada.';
    }
}
