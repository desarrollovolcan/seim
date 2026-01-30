<?php

class CrmController extends Controller
{
    private ClientsModel $clients;
    private CommercialBriefsModel $briefs;
    private SalesOrdersModel $orders;
    private SalesOrderItemsModel $orderItems;
    private ProductsModel $products;
    private ServiceRenewalsModel $renewals;
    private ServicesModel $services;
    private EmailQueueModel $queue;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->db = $db;
        $this->clients = new ClientsModel($db);
        $this->briefs = new CommercialBriefsModel($db);
        $this->orders = new SalesOrdersModel($db);
        $this->orderItems = new SalesOrderItemsModel($db);
        $this->products = new ProductsModel($db);
        $this->renewals = new ServiceRenewalsModel($db);
        $this->services = new ServicesModel($db);
        $this->queue = new EmailQueueModel($db);
    }

    public function hub(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $clients = $companyId ? $this->clients->active($companyId) : [];
        $this->render('crm/hub', [
            'title' => 'CRM Comercial',
            'pageTitle' => 'CRM Comercial',
            'clients' => $clients,
        ]);
    }

    public function reports(): void
    {
        $this->requireLogin();
        $range = $_GET['range'] ?? '30d';
        $statusFilter = $_GET['status'] ?? 'all';
        $startInput = $_GET['start'] ?? '';
        $endInput = $_GET['end'] ?? '';

        $now = new DateTimeImmutable('today');
        $startDate = $now->modify('-30 days');
        $endDate = $now;

        if ($range === 'quarter') {
            $month = (int)$now->format('n');
            $quarterStartMonth = (int)(floor(($month - 1) / 3) * 3 + 1);
            $startDate = $now->setDate((int)$now->format('Y'), $quarterStartMonth, 1);
            $endDate = $startDate->modify('+2 months')->modify('last day of this month');
        } elseif ($range === 'year') {
            $startDate = $now->setDate((int)$now->format('Y'), 1, 1);
            $endDate = $now->setDate((int)$now->format('Y'), 12, 31);
        } elseif ($range === 'custom' && $startInput !== '' && $endInput !== '') {
            try {
                $startDate = new DateTimeImmutable($startInput);
                $endDate = new DateTimeImmutable($endInput);
            } catch (Exception $e) {
                $startDate = $now->modify('-30 days');
                $endDate = $now;
            }
        }

        if ($startDate > $endDate) {
            $swap = $startDate;
            $startDate = $endDate;
            $endDate = $swap;
        }

        $startParam = $startDate->format('Y-m-d');
        $endParam = $endDate->format('Y-m-d');

        $isAdmin = (Auth::user()['role'] ?? '') === 'admin';
        $companyId = $isAdmin ? null : current_company_id();
        $companyFilter = $companyId ? ' AND company_id = :company_id' : '';
        $companyParams = $companyId ? ['company_id' => $companyId] : [];

        try {
            $billing = $this->db->fetch(
                'SELECT COALESCE(SUM(total),0) as total
                 FROM invoices
                 WHERE estado = "pagada" AND deleted_at IS NULL
                   AND fecha_emision BETWEEN :start AND :end' . $companyFilter,
                array_merge(['start' => $startParam, 'end' => $endParam], $companyParams)
            );

            $pipeline = $this->db->fetch(
                'SELECT COALESCE(SUM(total),0) as total, COUNT(*) as count
                 FROM quotes
                 WHERE estado = "pendiente"
                   AND fecha_emision BETWEEN :start AND :end' . $companyFilter,
                array_merge(['start' => $startParam, 'end' => $endParam], $companyParams)
            );

            $ticketsTotal = $this->db->fetch(
                'SELECT COUNT(*) as total
                 FROM support_tickets
                 WHERE created_at BETWEEN :start AND :end' . $companyFilter,
                array_merge(['start' => $startParam . ' 00:00:00', 'end' => $endParam . ' 23:59:59'], $companyParams)
            );
            $ticketsClosed = $this->db->fetch(
                'SELECT COUNT(*) as total
                 FROM support_tickets
                 WHERE status = "cerrado"
                   AND updated_at BETWEEN :start AND :end' . $companyFilter,
                array_merge(['start' => $startParam . ' 00:00:00', 'end' => $endParam . ' 23:59:59'], $companyParams)
            );
            $slaPercent = 0;
            if ((int)$ticketsTotal['total'] > 0) {
                $slaPercent = (int)round(((int)$ticketsClosed['total'] / (int)$ticketsTotal['total']) * 100);
            }

            $alerts = $this->db->fetch(
                'SELECT COUNT(*) as total
                 FROM invoices
                 WHERE estado = "vencida" AND deleted_at IS NULL
                   AND fecha_vencimiento BETWEEN :start AND :end' . $companyFilter,
                array_merge(['start' => $startParam, 'end' => $endParam], $companyParams)
            );

            $statusClause = '';
            $statusParams = [];
            if ($statusFilter !== 'all') {
                $statusClause = ' AND quotes.estado = :status';
                $statusParams = ['status' => $statusFilter];
            }

            $activities = $this->db->fetchAll(
                'SELECT quotes.*, clients.name as client_name
                 FROM quotes
                 JOIN clients ON quotes.client_id = clients.id
                 WHERE quotes.fecha_emision BETWEEN :start AND :end' . $companyFilter . $statusClause . '
                 ORDER BY quotes.fecha_emision DESC, quotes.id DESC
                 LIMIT 8',
                array_merge(['start' => $startParam, 'end' => $endParam], $companyParams, $statusParams)
            );
        } catch (PDOException $e) {
            log_message('error', 'Failed to load CRM reports: ' . $e->getMessage());
            $billing = ['total' => 0];
            $pipeline = ['total' => 0, 'count' => 0];
            $slaPercent = 0;
            $alerts = ['total' => 0];
            $activities = [];
        }

        $this->render('crm/reports', [
            'title' => 'Reportes CRM',
            'pageTitle' => 'Reportes CRM',
            'billingTotal' => (float)($billing['total'] ?? 0),
            'pipelineTotal' => (float)($pipeline['total'] ?? 0),
            'pipelineCount' => (int)($pipeline['count'] ?? 0),
            'slaPercent' => $slaPercent,
            'alertCount' => (int)($alerts['total'] ?? 0),
            'activities' => $activities,
            'filters' => [
                'range' => $range,
                'status' => $statusFilter,
                'start' => $startParam,
                'end' => $endParam,
            ],
        ]);
    }

    public function briefs(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $briefs = $this->db->fetchAll(
            'SELECT commercial_briefs.*, clients.name as client_name
             FROM commercial_briefs
             JOIN clients ON commercial_briefs.client_id = clients.id
             WHERE commercial_briefs.deleted_at IS NULL AND commercial_briefs.company_id = :company_id
             ORDER BY commercial_briefs.id DESC',
            ['company_id' => $companyId]
        );
        $clients = $this->clients->active($companyId);
        $this->render('crm/briefs', [
            'title' => 'Briefs Comerciales',
            'pageTitle' => 'Briefs Comerciales',
            'briefs' => $briefs,
            'clients' => $clients,
            'editBrief' => null,
        ]);
    }

    public function editBrief(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_GET['id'] ?? 0);
        $editBrief = $this->db->fetch(
            'SELECT commercial_briefs.*, clients.name as client_name
             FROM commercial_briefs
             JOIN clients ON commercial_briefs.client_id = clients.id
             WHERE commercial_briefs.deleted_at IS NULL AND commercial_briefs.company_id = :company_id AND commercial_briefs.id = :id',
            ['company_id' => $companyId, 'id' => $id]
        );
        if (!$editBrief) {
            flash('error', 'Brief comercial no encontrado.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $briefs = $this->db->fetchAll(
            'SELECT commercial_briefs.*, clients.name as client_name
             FROM commercial_briefs
             JOIN clients ON commercial_briefs.client_id = clients.id
             WHERE commercial_briefs.deleted_at IS NULL AND commercial_briefs.company_id = :company_id
             ORDER BY commercial_briefs.id DESC',
            ['company_id' => $companyId]
        );
        $clients = $this->clients->active($companyId);
        $this->render('crm/briefs', [
            'title' => 'Briefs Comerciales',
            'pageTitle' => 'Briefs Comerciales',
            'briefs' => $briefs,
            'clients' => $clients,
            'editBrief' => $editBrief,
        ]);
    }

    public function storeBrief(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $statusInput = $_POST['status'] ?? 'nuevo';
        $allowedStatuses = ['nuevo', 'en_revision', 'en_ejecucion', 'aprobado', 'descartado'];
        $status = in_array($statusInput, $allowedStatuses, true) ? $statusInput : 'nuevo';
        $data = [
            'company_id' => $companyId,
            'client_id' => $clientId,
            'title' => trim($_POST['title'] ?? ''),
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'contact_email' => trim($_POST['contact_email'] ?? ''),
            'contact_phone' => trim($_POST['contact_phone'] ?? ''),
            'service_summary' => trim($_POST['service_summary'] ?? ''),
            'expected_budget' => $_POST['expected_budget'] !== '' ? (float)$_POST['expected_budget'] : null,
            'desired_start_date' => $_POST['desired_start_date'] !== '' ? $_POST['desired_start_date'] : null,
            'status' => $status,
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($data['title'] === '') {
            flash('error', 'Ingresa un nombre para el brief.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $this->briefs->create($data);
        audit($this->db, Auth::user()['id'], 'create', 'commercial_briefs');
        flash('success', 'Brief comercial creado correctamente.');
        $this->redirect('index.php?route=crm/briefs');
    }

    public function updateBrief(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $brief = $this->db->fetch(
            'SELECT id FROM commercial_briefs WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$brief) {
            flash('error', 'Brief comercial no encontrado.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $statusInput = $_POST['status'] ?? 'nuevo';
        $allowedStatuses = ['nuevo', 'en_revision', 'en_ejecucion', 'aprobado', 'descartado'];
        $status = in_array($statusInput, $allowedStatuses, true) ? $statusInput : 'nuevo';
        $data = [
            'client_id' => $clientId,
            'title' => trim($_POST['title'] ?? ''),
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'contact_email' => trim($_POST['contact_email'] ?? ''),
            'contact_phone' => trim($_POST['contact_phone'] ?? ''),
            'service_summary' => trim($_POST['service_summary'] ?? ''),
            'expected_budget' => $_POST['expected_budget'] !== '' ? (float)$_POST['expected_budget'] : null,
            'desired_start_date' => $_POST['desired_start_date'] !== '' ? $_POST['desired_start_date'] : null,
            'status' => $status,
            'notes' => trim($_POST['notes'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($data['title'] === '') {
            flash('error', 'Ingresa un nombre para el brief.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $this->db->execute(
            'UPDATE commercial_briefs
             SET client_id = :client_id,
                 title = :title,
                 contact_name = :contact_name,
                 contact_email = :contact_email,
                 contact_phone = :contact_phone,
                 service_summary = :service_summary,
                 expected_budget = :expected_budget,
                 desired_start_date = :desired_start_date,
                 status = :status,
                 notes = :notes,
                 updated_at = :updated_at
             WHERE id = :id AND company_id = :company_id',
            array_merge($data, ['id' => $id, 'company_id' => $companyId])
        );
        audit($this->db, Auth::user()['id'], 'update', 'commercial_briefs', $id);
        flash('success', 'Brief comercial actualizado correctamente.');
        $this->redirect('index.php?route=crm/briefs');
    }

    public function executeBrief(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $brief = $this->db->fetch(
            'SELECT id FROM commercial_briefs WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$brief) {
            flash('error', 'Brief comercial no encontrado.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $this->db->execute(
            'UPDATE commercial_briefs SET status = :status, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['status' => 'en_ejecucion', 'id' => $id, 'company_id' => $companyId]
        );
        audit($this->db, Auth::user()['id'], 'update', 'commercial_briefs', $id);
        flash('success', 'Brief comercial marcado en ejecución.');
        $this->redirect('index.php?route=crm/briefs');
    }

    public function deleteBrief(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $brief = $this->db->fetch(
            'SELECT id FROM commercial_briefs WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$brief) {
            flash('error', 'Brief comercial no encontrado.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $this->db->execute(
            'UPDATE commercial_briefs SET deleted_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        audit($this->db, Auth::user()['id'], 'delete', 'commercial_briefs', $id);
        flash('success', 'Brief comercial eliminado correctamente.');
        $this->redirect('index.php?route=crm/briefs');
    }

    public function reportBrief(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            flash('error', 'Brief comercial no encontrado.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $brief = $this->db->fetch(
            'SELECT id FROM commercial_briefs WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$brief) {
            flash('error', 'Brief comercial no encontrado.');
            $this->redirect('index.php?route=crm/briefs');
        }
        $GLOBALS['db'] = $this->db;
        $_GET['id'] = $id;
        try {
            require_once __DIR__ . '/../../informes/briefs_create.php';
        } catch (Throwable $e) {
            log_message('error', 'Failed to generate brief report: ' . $e->getMessage());
            http_response_code(500);
            echo 'No se pudo generar el informe.';
        }
        exit;
    }

    public function orders(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $orders = $this->db->fetchAll(
            'SELECT sales_orders.*, clients.name as client_name, commercial_briefs.title as brief_title
             FROM sales_orders
             JOIN clients ON sales_orders.client_id = clients.id
             LEFT JOIN commercial_briefs ON sales_orders.brief_id = commercial_briefs.id
             WHERE sales_orders.deleted_at IS NULL AND sales_orders.company_id = :company_id
             ORDER BY sales_orders.id DESC',
            ['company_id' => $companyId]
        );
        $clients = $this->clients->active($companyId);
        $briefs = $this->briefs->active($companyId);
        $products = $this->products->active($companyId);
        $this->render('crm/orders', [
            'title' => 'Órdenes de Venta',
            'pageTitle' => 'Órdenes de Venta',
            'orders' => $orders,
            'clients' => $clients,
            'briefs' => $briefs,
            'products' => $products,
        ]);
    }

    public function storeOrder(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=crm/orders');
        }
        $briefId = (int)($_POST['brief_id'] ?? 0);
        $briefExists = null;
        if ($briefId > 0) {
            $briefExists = $this->db->fetch(
                'SELECT id FROM commercial_briefs WHERE id = :id AND company_id = :company_id',
                ['id' => $briefId, 'company_id' => $companyId]
            );
        }
        $orderNumber = trim($_POST['order_number'] ?? '');
        if ($orderNumber === '') {
            $orderNumber = 'OV-' . date('Ymd-His');
        }
        $items = $this->collectOrderItems($companyId);
        if (empty($items)) {
            flash('error', 'Agrega al menos un producto a la orden.');
            $this->redirect('index.php?route=crm/orders');
        }

        $total = array_sum(array_map(static fn(array $item) => $item['subtotal'], $items));
        $data = [
            'company_id' => $companyId,
            'client_id' => $clientId,
            'brief_id' => $briefExists ? $briefId : null,
            'order_number' => $orderNumber,
            'order_date' => $_POST['order_date'] !== '' ? $_POST['order_date'] : date('Y-m-d'),
            'status' => $_POST['status'] ?? 'pendiente',
            'total' => $total,
            'currency' => $_POST['currency'] ?? 'CLP',
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($data['total'] <= 0) {
            flash('error', 'Ingresa un total válido para la orden.');
            $this->redirect('index.php?route=crm/orders');
        }

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $orderId = $this->orders->create($data);
            foreach ($items as $item) {
                $this->orderItems->create([
                    'sales_order_id' => $orderId,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            audit($this->db, Auth::user()['id'], 'create', 'sales_orders', $orderId);
            $pdo->commit();
            flash('success', 'Orden de venta creada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error al crear orden de venta: ' . $e->getMessage());
            flash('error', 'No pudimos guardar la orden. Inténtalo nuevamente.');
        }

        $this->redirect('index.php?route=crm/orders');
    }

    private function collectOrderItems(int $companyId): array
    {
        $productIds = $_POST['order_product_id'] ?? [];
        $quantities = $_POST['order_quantity'] ?? [];
        $unitPrices = $_POST['order_unit_price'] ?? [];
        $items = [];

        foreach ($productIds as $index => $productId) {
            $productId = (int)$productId;
            $quantity = max(0, (int)($quantities[$index] ?? 0));
            $unitPrice = max(0.0, (float)($unitPrices[$index] ?? 0));
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $product = $this->products->findForCompany($productId, $companyId);
            if (!$product) {
                continue;
            }
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
            ];
        }

        return $items;
    }

    public function renewals(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $renewals = $this->db->fetchAll(
            'SELECT service_renewals.*, clients.name as client_name, services.name as service_name
             FROM service_renewals
             JOIN clients ON service_renewals.client_id = clients.id
             LEFT JOIN services ON service_renewals.service_id = services.id
             WHERE service_renewals.deleted_at IS NULL AND service_renewals.company_id = :company_id
             ORDER BY service_renewals.id DESC',
            ['company_id' => $companyId]
        );
        $clients = $this->clients->active($companyId);
        $services = $this->services->active($companyId);
        $this->render('crm/renewals', [
            'title' => 'Renovaciones',
            'pageTitle' => 'Renovaciones',
            'renewals' => $renewals,
            'clients' => $clients,
            'services' => $services,
        ]);
    }

    public function storeRenewal(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para continuar.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=crm/renewals');
        }
        $serviceId = (int)($_POST['service_id'] ?? 0);
        $serviceExists = null;
        if ($serviceId > 0) {
            $serviceExists = $this->db->fetch(
                'SELECT id FROM services WHERE id = :id AND company_id = :company_id',
                ['id' => $serviceId, 'company_id' => $companyId]
            );
        }
        $data = [
            'company_id' => $companyId,
            'client_id' => $clientId,
            'service_id' => $serviceExists ? $serviceId : null,
            'renewal_date' => $_POST['renewal_date'] !== '' ? $_POST['renewal_date'] : date('Y-m-d'),
            'status' => $_POST['status'] ?? 'pendiente',
            'amount' => (float)($_POST['amount'] ?? 0),
            'currency' => $_POST['currency'] ?? 'CLP',
            'reminder_days' => (int)($_POST['reminder_days'] ?? 15),
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($data['amount'] <= 0) {
            flash('error', 'Ingresa un monto válido para la renovación.');
            $this->redirect('index.php?route=crm/renewals');
        }
        $this->renewals->create($data);
        audit($this->db, Auth::user()['id'], 'create', 'service_renewals');
        flash('success', 'Renovación registrada correctamente.');
        $this->redirect('index.php?route=crm/renewals');
    }

    public function approveRenewal(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        $id = (int)($_POST['id'] ?? 0);
        $renewal = $this->db->fetch(
            'SELECT id FROM service_renewals WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$renewal) {
            flash('error', 'Renovación no encontrada para esta empresa.');
            $this->redirect('index.php?route=crm/renewals');
        }
        $this->db->execute(
            'UPDATE service_renewals SET status = :status, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['status' => 'renovado', 'id' => $id, 'company_id' => $companyId]
        );
        $this->applyRenewalToService($renewal['service_id'] ?? null, $renewal['renewal_date'] ?? null, $companyId);
        audit($this->db, Auth::user()['id'], 'update', 'service_renewals', $id);
        flash('success', 'Renovación aprobada correctamente.');
        $this->redirect('index.php?route=crm/renewals');
    }

    public function updateRenewal(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        $id = (int)($_POST['id'] ?? 0);
        $renewal = $this->db->fetch(
            'SELECT * FROM service_renewals WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$renewal) {
            flash('error', 'Renovación no encontrada para esta empresa.');
            $this->redirect('index.php?route=crm/renewals');
        }

        $status = $_POST['status'] ?? 'pendiente';
        $allowedStatuses = ['pendiente', 'en_negociacion', 'renovado', 'no_renovado'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pendiente';
        }

        $data = [
            'renewal_date' => $_POST['renewal_date'] !== '' ? $_POST['renewal_date'] : $renewal['renewal_date'],
            'status' => $status,
            'amount' => (float)($_POST['amount'] ?? $renewal['amount']),
            'currency' => $_POST['currency'] ?? $renewal['currency'],
            'reminder_days' => (int)($_POST['reminder_days'] ?? $renewal['reminder_days']),
            'notes' => trim($_POST['notes'] ?? $renewal['notes']),
        ];

        if ($data['amount'] <= 0) {
            flash('error', 'Ingresa un monto válido para la renovación.');
            $this->redirect('index.php?route=crm/renewals');
        }

        $this->db->execute(
            'UPDATE service_renewals
             SET renewal_date = :renewal_date, status = :status, amount = :amount, currency = :currency, reminder_days = :reminder_days, notes = :notes, updated_at = NOW()
             WHERE id = :id AND company_id = :company_id',
            [
                'renewal_date' => $data['renewal_date'],
                'status' => $data['status'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reminder_days' => $data['reminder_days'],
                'notes' => $data['notes'],
                'id' => $id,
                'company_id' => $companyId,
            ]
        );

        audit($this->db, Auth::user()['id'], 'update', 'service_renewals', $id);
        if ($data['status'] === 'renovado') {
            $this->applyRenewalToService($renewal['service_id'] ?? null, $data['renewal_date'], $companyId);
        }
        flash('success', 'Renovación actualizada correctamente.');
        $this->redirect('index.php?route=crm/renewals');
    }

    public function deleteRenewal(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        $id = (int)($_POST['id'] ?? 0);
        $renewal = $this->db->fetch(
            'SELECT id FROM service_renewals WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$renewal) {
            flash('error', 'Renovación no encontrada para esta empresa.');
            $this->redirect('index.php?route=crm/renewals');
        }

        $this->db->execute(
            'UPDATE service_renewals SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        audit($this->db, Auth::user()['id'], 'delete', 'service_renewals', $id);
        flash('success', 'Renovación eliminada correctamente.');
        $this->redirect('index.php?route=crm/renewals');
    }

    public function sendRenewalEmail(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        $id = (int)($_POST['id'] ?? 0);
        $renewal = $this->db->fetch(
            'SELECT service_renewals.*, clients.name as client_name, clients.email, clients.billing_email, services.name as service_name
             FROM service_renewals
             JOIN clients ON service_renewals.client_id = clients.id
             LEFT JOIN services ON service_renewals.service_id = services.id
             WHERE service_renewals.id = :id AND service_renewals.company_id = :company_id AND service_renewals.deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );

        if (!$renewal) {
            flash('error', 'Renovación no encontrada para esta empresa.');
            $this->redirect('index.php?route=crm/renewals');
        }

        if (($renewal['status'] ?? '') !== 'renovado') {
            flash('error', 'Solo se pueden notificar renovaciones marcadas como renovadas.');
            $this->redirect('index.php?route=crm/renewals');
        }

        $email = $renewal['billing_email'] ?? $renewal['email'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'El cliente no tiene un correo válido para enviar la notificación.');
            $this->redirect('index.php?route=crm/renewals');
        }

        $settings = new SettingsModel($this->db);
        $company = $settings->get('company', []);
        $context = [
            'cliente_nombre' => $renewal['client_name'] ?? '',
            'servicio_nombre' => $renewal['service_name'] ?? '',
            'monto_total' => format_currency((float)($renewal['amount'] ?? 0)),
            'fecha_renovacion' => format_date($renewal['renewal_date']),
            'empresa_nombre' => $company['name'] ?? 'Go Creative',
        ];

        $template = $this->db->fetch(
            'SELECT body_html FROM email_templates WHERE type = :type AND deleted_at IS NULL AND company_id = :company_id ORDER BY id DESC LIMIT 1',
            ['type' => 'informativa', 'company_id' => $companyId]
        );
        $subject = 'Renovación exitosa de servicio';
        $body = '';
        if ($template) {
            $body = render_template_vars($template['body_html'], $context);
        } else {
            $path = __DIR__ . '/../../storage/email_templates/informativa.html';
            $fallback = is_file($path) ? file_get_contents($path) : '';
            $body = render_template_vars($fallback, $context);
        }

        $this->queue->create([
            'company_id' => $companyId,
            'client_id' => $renewal['client_id'],
            'template_id' => null,
            'subject' => $subject,
            'body_html' => $body,
            'type' => 'informativa',
            'status' => 'pending',
            'scheduled_at' => date('Y-m-d H:i:s'),
            'tries' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        flash('success', 'Correo de renovación exitosa en cola para envío.');
        $this->redirect('index.php?route=crm/renewals');
    }

    private function applyRenewalToService(?int $serviceId, ?string $renewalDate, int $companyId): void
    {
        if (!$serviceId || !$renewalDate) {
            return;
        }

        $service = $this->db->fetch(
            'SELECT id, billing_cycle FROM services WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $serviceId, 'company_id' => $companyId]
        );

        if (!$service) {
            return;
        }

        $billingCycle = $service['billing_cycle'] ?? 'anual';
        $renewalDateTime = DateTime::createFromFormat('Y-m-d', $renewalDate);
        if ($renewalDateTime === false) {
            try {
                $renewalDateTime = new DateTime($renewalDate);
            } catch (Exception $e) {
                return;
            }
        }
        $newDueDate = clone $renewalDateTime;

        switch ($billingCycle) {
            case 'mensual':
                $newDueDate->modify('+1 month');
                break;
            case 'anual':
                $newDueDate->modify('+1 year');
                break;
            case 'trimestral':
                $newDueDate->modify('+3 months');
                break;
            case 'semestral':
                $newDueDate->modify('+6 months');
                break;
            default:
                $newDueDate->modify('+1 year');
        }

        $this->db->execute(
            'UPDATE services
             SET due_date = :due_date,
                 delete_date = :delete_date,
                 status = :status,
                 updated_at = NOW()
             WHERE id = :id AND company_id = :company_id',
            [
                'due_date' => $newDueDate->format('Y-m-d'),
                'delete_date' => $newDueDate->format('Y-m-d'),
                'status' => 'renovado',
                'id' => $serviceId,
                'company_id' => $companyId,
            ]
        );
    }
}
