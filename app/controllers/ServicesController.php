<?php

class ServicesController extends Controller
{
    private ServicesModel $services;
    private ClientsModel $clients;
    private EmailQueueModel $queue;
    private EmailTemplatesModel $templates;
    private ServiceTypesModel $serviceTypes;
    private SystemServicesModel $systemServices;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->services = new ServicesModel($db);
        $this->clients = new ClientsModel($db);
        $this->queue = new EmailQueueModel($db);
        $this->templates = new EmailTemplatesModel($db);
        $this->serviceTypes = new ServiceTypesModel($db);
        $this->systemServices = new SystemServicesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }

        $this->processExpiredServices($companyId);

        $services = $this->services->active($companyId);
        $this->render('services/index', [
            'title' => 'Servicios',
            'pageTitle' => 'Servicios',
            'services' => $services,
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
        $serviceTypes = $this->serviceTypes->all('company_id = :company_id', ['company_id' => $companyId]);
        $systemServices = $this->systemServices->allWithType($companyId);
        $selectedClientId = (int)($_GET['client_id'] ?? 0);
        $this->render('services/create', [
            'title' => 'Nuevo Servicio',
            'pageTitle' => 'Nuevo Servicio',
            'clients' => $clients,
            'serviceTypes' => $serviceTypes,
            'systemServices' => $systemServices,
            'selectedClientId' => $selectedClientId,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $startDate = trim($_POST['start_date'] ?? '');
        $dueDate = trim($_POST['due_date'] ?? '');
        $deleteDate = trim($_POST['delete_date'] ?? '');
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=services/create');
        }
        $serviceTypeId = (int)($_POST['service_type_id'] ?? 0);
        $serviceType = $this->db->fetch(
            'SELECT * FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $serviceTypeId, 'company_id' => $companyId]
        );
        if (!$serviceType) {
            flash('error', 'Tipo de servicio no encontrado para esta empresa.');
            $this->redirect('index.php?route=services/create');
        }
        $systemServiceId = (int)($_POST['system_service_id'] ?? 0);
        $systemService = null;
        if ($systemServiceId > 0) {
            $systemService = $this->db->fetch(
                'SELECT * FROM system_services WHERE id = :id AND company_id = :company_id',
                ['id' => $systemServiceId, 'company_id' => $companyId]
            );
            if (!$systemService || (int)$systemService['service_type_id'] !== $serviceTypeId) {
                flash('error', 'Servicio no encontrado para esta empresa.');
                $this->redirect('index.php?route=services/create');
            }
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '' && $systemService) {
            $name = $systemService['name'] ?? '';
        }
        $cost = (float)($_POST['cost'] ?? 0);
        if ($cost <= 0 && $systemService) {
            $cost = (float)($systemService['cost'] ?? 0);
        }
        $currency = $_POST['currency'] ?? '';
        if ($currency === '' && $systemService) {
            $currency = $systemService['currency'] ?? 'CLP';
        }
        $data = [
            'company_id' => $companyId,
            'client_id' => $clientId,
            'service_type' => $serviceType['name'],
            'name' => $name,
            'cost' => $cost,
            'currency' => $currency !== '' ? $currency : 'CLP',
            'billing_cycle' => $_POST['billing_cycle'] ?? 'anual',
            'start_date' => $startDate !== '' ? $startDate : null,
            'due_date' => $dueDate !== '' ? $dueDate : null,
            'delete_date' => $deleteDate !== '' ? $deleteDate : null,
            'notice_days_1' => (int)($_POST['notice_days_1'] ?? 15),
            'notice_days_2' => (int)($_POST['notice_days_2'] ?? 5),
            'status' => $_POST['status'] ?? 'activo',
            'auto_invoice' => isset($_POST['auto_invoice']) ? 1 : 0,
            'auto_email' => isset($_POST['auto_email']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $serviceId = $this->services->create($data);
        $service = $this->services->find($serviceId);
        if ($service) {
            $client = $this->db->fetch(
                'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
                ['id' => $service['client_id'], 'company_id' => current_company_id()]
            );
            if ($client) {
                $this->enqueueServiceEmails($service, $client);
            }
        }
        audit($this->db, Auth::user()['id'], 'create', 'services');
        flash('success', 'Servicio creado correctamente.');
        $this->redirect('index.php?route=services');
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
        $service = $this->db->fetch(
            'SELECT * FROM services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$service) {
            $this->redirect('index.php?route=services');
        }
        $renewals = $this->db->fetchAll(
            'SELECT * FROM service_renewals WHERE service_id = :service_id AND company_id = :company_id AND deleted_at IS NULL ORDER BY renewal_date DESC, id DESC',
            ['service_id' => $id, 'company_id' => $companyId]
        );
        $clients = $this->clients->active($companyId);
        $serviceTypes = $this->serviceTypes->all('company_id = :company_id', ['company_id' => $companyId]);
        $systemServices = $this->systemServices->allWithType($companyId);
        $selectedServiceTypeId = null;
        foreach ($serviceTypes as $type) {
            if (($type['name'] ?? '') === ($service['service_type'] ?? '')) {
                $selectedServiceTypeId = (int)$type['id'];
                break;
            }
        }
        $selectedSystemServiceId = null;
        if ($selectedServiceTypeId !== null && !empty($service['name'])) {
            foreach ($systemServices as $systemService) {
                if ((int)($systemService['service_type_id'] ?? 0) !== $selectedServiceTypeId) {
                    continue;
                }
                if (strcasecmp(trim($systemService['name'] ?? ''), trim($service['name'] ?? '')) === 0) {
                    $selectedSystemServiceId = (int)$systemService['id'];
                    break;
                }
            }
        }
        $this->render('services/edit', [
            'title' => 'Editar Servicio',
            'pageTitle' => 'Editar Servicio',
            'service' => $service,
            'clients' => $clients,
            'serviceTypes' => $serviceTypes,
            'systemServices' => $systemServices,
            'selectedServiceTypeId' => $selectedServiceTypeId,
            'selectedSystemServiceId' => $selectedSystemServiceId,
            'renewals' => $renewals,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $service = $this->db->fetch(
            'SELECT id FROM services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$service) {
            flash('error', 'Servicio no encontrado para esta empresa.');
            $this->redirect('index.php?route=services');
        }
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $startDate = trim($_POST['start_date'] ?? '');
        $dueDate = trim($_POST['due_date'] ?? '');
        $deleteDate = trim($_POST['delete_date'] ?? '');
        $serviceTypeId = (int)($_POST['service_type_id'] ?? 0);
        $serviceType = $this->db->fetch(
            'SELECT * FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $serviceTypeId, 'company_id' => $companyId]
        );
        if (!$serviceType) {
            flash('error', 'Tipo de servicio no encontrado para esta empresa.');
            $this->redirect('index.php?route=services/edit&id=' . $id);
        }
        $systemServiceId = (int)($_POST['system_service_id'] ?? 0);
        $systemService = null;
        if ($systemServiceId > 0) {
            $systemService = $this->db->fetch(
                'SELECT * FROM system_services WHERE id = :id AND company_id = :company_id',
                ['id' => $systemServiceId, 'company_id' => $companyId]
            );
            if (!$systemService || (int)$systemService['service_type_id'] !== $serviceTypeId) {
                flash('error', 'Servicio no encontrado para esta empresa.');
                $this->redirect('index.php?route=services/edit&id=' . $id);
            }
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '' && $systemService) {
            $name = $systemService['name'] ?? '';
        }
        $cost = (float)($_POST['cost'] ?? 0);
        if ($cost <= 0 && $systemService) {
            $cost = (float)($systemService['cost'] ?? 0);
        }
        $currency = $_POST['currency'] ?? '';
        if ($currency === '' && $systemService) {
            $currency = $systemService['currency'] ?? 'CLP';
        }
        $data = [
            'client_id' => (int)($_POST['client_id'] ?? 0),
            'service_type' => $serviceType['name'],
            'name' => $name,
            'cost' => $cost,
            'currency' => $currency !== '' ? $currency : 'CLP',
            'billing_cycle' => $_POST['billing_cycle'] ?? 'anual',
            'start_date' => $startDate !== '' ? $startDate : null,
            'due_date' => $dueDate !== '' ? $dueDate : null,
            'delete_date' => $deleteDate !== '' ? $deleteDate : null,
            'notice_days_1' => (int)($_POST['notice_days_1'] ?? 15),
            'notice_days_2' => (int)($_POST['notice_days_2'] ?? 5),
            'status' => $_POST['status'] ?? 'activo',
            'auto_invoice' => isset($_POST['auto_invoice']) ? 1 : 0,
            'auto_email' => isset($_POST['auto_email']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->services->update($id, $data);
        audit($this->db, Auth::user()['id'], 'update', 'services', $id);
        flash('success', 'Servicio actualizado correctamente.');
        $this->redirect('index.php?route=services');
    }

    public function show(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $companyId = current_company_id();
        $service = $this->db->fetch(
            'SELECT * FROM services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$service) {
            $this->redirect('index.php?route=services');
        }
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $service['client_id'], 'company_id' => $companyId]
        );
        $invoices = $this->db->fetchAll(
            'SELECT * FROM invoices WHERE service_id = :id AND company_id = :company_id ORDER BY id DESC',
            ['id' => $id, 'company_id' => $companyId]
        );
        $renewals = $this->db->fetchAll(
            'SELECT * FROM service_renewals WHERE service_id = :service_id AND company_id = :company_id AND deleted_at IS NULL ORDER BY renewal_date DESC, id DESC',
            ['service_id' => $id, 'company_id' => $companyId]
        );
        $this->render('services/show', [
            'title' => 'Detalle Servicio',
            'pageTitle' => 'Detalle Servicio',
            'service' => $service,
            'client' => $client,
            'invoices' => $invoices,
            'renewals' => $renewals,
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $service = $this->db->fetch(
            'SELECT id FROM services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$service) {
            flash('error', 'Servicio no encontrado para esta empresa.');
            $this->redirect('index.php?route=services');
        }
        $invoiceCount = $this->db->fetch('SELECT COUNT(*) as total FROM invoices WHERE service_id = :id AND deleted_at IS NULL', ['id' => $id]);
        $quoteCount = $this->db->fetch('SELECT COUNT(*) as total FROM quotes WHERE service_id = :id', ['id' => $id]);
        $blocked = [];
        if (!empty($invoiceCount['total'])) {
            $blocked[] = 'facturas';
        }
        if (!empty($quoteCount['total'])) {
            $blocked[] = 'cotizaciones';
        }
        if (!empty($blocked)) {
            flash('error', 'No se puede eliminar el servicio porque tiene registros asociados: ' . implode(', ', $blocked) . '.');
            $this->redirect('index.php?route=services');
        }
        $this->services->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'services', $id);
        flash('success', 'Servicio eliminado correctamente.');
        $this->redirect('index.php?route=services');
    }

    public function generateInvoice(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $companyId = current_company_id();
        $service = $this->db->fetch(
            'SELECT * FROM services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$service) {
            $this->redirect('index.php?route=services');
        }

        $settings = new SettingsModel($this->db);
        $prefix = $settings->get('invoice_prefix', 'FAC-');
        $invoicesModel = new InvoicesModel($this->db);
        $number = $invoicesModel->nextNumber($prefix, $companyId);

        $invoiceId = $invoicesModel->create([
            'company_id' => $companyId,
            'client_id' => $service['client_id'],
            'service_id' => $service['id'],
            'numero' => $number,
            'fecha_emision' => date('Y-m-d'),
            'fecha_vencimiento' => $service['due_date'] ?? date('Y-m-d'),
            'estado' => 'pendiente',
            'subtotal' => $service['cost'],
            'impuestos' => 0,
            'total' => $service['cost'],
            'notas' => 'Factura generada desde servicio',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $itemsModel = new InvoiceItemsModel($this->db);
        $itemsModel->create([
            'invoice_id' => $invoiceId,
            'descripcion' => $service['name'],
            'cantidad' => 1,
            'precio_unitario' => $service['cost'],
            'total' => $service['cost'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        flash('success', 'Factura generada desde el servicio.');
        $this->redirect('index.php?route=invoices/show&id=' . $invoiceId);
    }

    private function enqueueServiceEmails(array $service, array $client): void
    {
        $settings = new SettingsModel($this->db);
        $company = $settings->get('company', []);
        $billingDefaults = $settings->get('billing_defaults', []);
        $sendTime = $billingDefaults['send_time'] ?? '09:00';
        $timezone = $billingDefaults['timezone'] ?? ($this->config['app']['timezone'] ?? 'UTC');
        $scheduledNow = (new DateTimeImmutable('now', new DateTimeZone($timezone)))->format('Y-m-d H:i:s');
        $companyId = current_company_id();
        $invoice = $this->db->fetch(
            'SELECT id, numero, total FROM invoices WHERE service_id = :service_id AND company_id = :company_id AND deleted_at IS NULL ORDER BY id DESC LIMIT 1',
            ['service_id' => $service['id'], 'company_id' => $companyId]
        );
        $invoiceNumber = (string)($invoice['numero'] ?? '');
        $serviceName = (string)($service['name'] ?? '');
        $amount = (float)($invoice['total'] ?? $service['cost'] ?? 0);
        $currency = (string)($service['currency'] ?? 'CLP');
        $paymentEmail = '';
        $clientEmail = $client['billing_email'] ?? $client['email'] ?? '';
        if (filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            $paymentEmail = $clientEmail;
        }
        $flowConfig = $settings->get('flow_payment_config', []);
        $flowLink = null;
        if ($paymentEmail !== '') {
            $flowLink = create_flow_payment_link($flowConfig, [
                'commerce_order' => $invoiceNumber !== '' ? $invoiceNumber : ('SERV-' . $service['id'] . '-' . date('YmdHis')),
                'subject' => 'Pago factura ' . ($invoiceNumber !== '' ? '#' . $invoiceNumber . ' - ' : '') . $serviceName,
                'currency' => $currency,
                'amount' => number_format($amount, 0, '.', ''),
                'email' => $paymentEmail,
            ]);
        }

        $context = [
            'cliente_nombre' => $client['name'] ?? '',
            'servicio_nombre' => $service['name'] ?? '',
            'dominio' => ($service['service_type'] ?? '') === 'dominio' ? ($service['name'] ?? '') : '',
            'hosting' => ($service['service_type'] ?? '') === 'hosting' ? ($service['name'] ?? '') : '',
            'monto_total' => format_currency($amount),
            'fecha_vencimiento' => $service['due_date'] ?? '',
            'fecha_eliminacion' => $service['delete_date'] ?? ($service['due_date'] ?? ''),
            'link_pago' => $flowLink ?? '',
            'numero_factura' => $invoiceNumber,
            'detalle_factura' => $serviceName,
        ];

        $this->queue->create([
            'company_id' => $companyId,
            'client_id' => $client['id'],
            'template_id' => $this->getTemplateId('Registro de servicio'),
            'subject' => 'Registro del servicio con éxito',
            'body_html' => $this->renderTemplateOrFallback(
                'Registro de servicio',
                $this->renderServiceEmail(
                    'Registro del servicio con éxito',
                    'Hemos registrado tu servicio correctamente. Te mantendremos informado sobre los próximos vencimientos.',
                    '#166534',
                    '#dcfce7',
                    $company,
                    $context
                ),
                $context
            ),
            'type' => 'informativa',
            'status' => 'pending',
            'scheduled_at' => $scheduledNow,
            'tries' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($service['due_date'])) {
            $dueDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $service['due_date'] . ' ' . $sendTime, new DateTimeZone($timezone))
                ?: new DateTimeImmutable($service['due_date'] . ' ' . $sendTime, new DateTimeZone($timezone));

            $reminders = [
                ['days' => 15, 'subject' => 'Primer aviso: vence en 15 días', 'template' => 'Cobranza 15 días'],
                ['days' => 10, 'subject' => 'Segundo aviso: vence en 10 días', 'template' => 'Cobranza 10 días'],
                ['days' => 5, 'subject' => 'Tercer aviso: vence en 5 días', 'template' => 'Cobranza 5 días'],
            ];

            foreach ($reminders as $reminder) {
                $scheduledAt = $dueDate->sub(new DateInterval('P' . $reminder['days'] . 'D'))->format('Y-m-d H:i:s');
                $this->queue->create([
                    'company_id' => $companyId,
                    'client_id' => $client['id'],
                    'template_id' => $this->getTemplateId($reminder['template']),
                    'subject' => $reminder['subject'],
                    'body_html' => $this->renderTemplateOrFallback(
                        $reminder['template'],
                        $this->renderServiceEmail(
                            $reminder['subject'],
                            'Te recordamos que tu servicio está próximo a vencer. Evita la suspensión realizando el pago a tiempo.',
                            '#9a3412',
                            '#ffedd5',
                            $company,
                            $context
                        ),
                        $context
                    ),
                    'type' => 'cobranza',
                    'status' => 'pending',
                    'scheduled_at' => $scheduledAt,
                    'tries' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $suspensionSubject = 'Servicio suspendido por vencimiento';
            $this->queue->create([
                'company_id' => $companyId,
                'client_id' => $client['id'],
                'template_id' => $this->getTemplateId('Servicio suspendido'),
                'subject' => $suspensionSubject,
                'body_html' => $this->renderTemplateOrFallback(
                    'Servicio suspendido',
                    $this->renderServiceEmail(
                        $suspensionSubject,
                        'Debido al no pago, el servicio se encuentra vencido y será suspendido en la fecha indicada.',
                        '#7f1d1d',
                        '#fee2e2',
                        $company,
                        $context
                    ),
                    $context
                ),
                'type' => 'cobranza',
                'status' => 'pending',
                'scheduled_at' => $dueDate->format('Y-m-d H:i:s'),
                'tries' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function renderServiceEmail(
        string $title,
        string $intro,
        string $accentColor,
        string $accentBackground,
        array $company,
        array $context
    ): string {
        $companyName = $company['name'] ?? 'Go Creative';
        $companyRut = $company['rut'] ?? '';
        $companyBank = $company['bank'] ?? '';
        $companyAccountType = $company['account_type'] ?? '';
        $companyAccountNumber = $company['account_number'] ?? '';
        $companyEmail = $company['email'] ?? 'cobranza@gocreative.cl';
        $signature = $company['signature'] ?? 'Equipo Go Creative';

        $html = '
<p>&nbsp;</p>
<div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
  Aviso de eliminación: su sitio web, correos del dominio y presencia en Google asociados a {{dominio}} serán dados de baja por no pago.
</div>

<table style="background:#f6f7f9; padding:28px 16px;" role="presentation" width="100%" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td align="center">
        <table style="max-width:640px;" role="presentation" width="640" cellspacing="0" cellpadding="0">
          <tbody>

            <tr>
              <td style="color:#6b7280; font-size:13px; letter-spacing:.08em; text-transform:uppercase; padding-bottom:12px;">
                ' . e($companyName) . ' · Cobranza
              </td>
            </tr>

            <tr>
              <td style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; padding:22px;">

                <h1 style="margin:0 0 12px 0; font-size:20px; color:' . e($accentColor) . ';">
                  ' . e($title) . ' – {{dominio}}
                </h1>

                <p style="margin:0 0 16px 0; font-size:14px; line-height:1.6; color:#374151;">
                  Estimado/a {{cliente_nombre}},<br /><br />
                  ' . e($intro) . '
                </p>

                <div style="background:' . e($accentBackground) . '; border:1px solid #fecaca; border-radius:12px; padding:14px; margin-bottom:18px;">
                  <p style="margin:0; font-size:14px; line-height:1.6; color:' . e($accentColor) . ';">
                    <strong>Consecuencias de la eliminación:</strong><br />
                    &bull; <strong>Baja definitiva del sitio web</strong> (la página dejará de estar disponible).<br />
                    &bull; <strong>Desactivación de los correos asociados al dominio</strong> (ej.: contacto@{{dominio}}).<br />
                    &bull; Pérdida de continuidad en la <strong>presencia en Google y en la web</strong>, afectando la visibilidad
                    del negocio y provocando que <strong>potenciales clientes no puedan encontrar ni contactar a su empresa</strong>.
                  </p>
                </div>

                <h2 style="font-size:14px; margin:0 0 8px 0; color:#111827;">
                  Detalle de servicios
                </h2>

                <table style="border-collapse:collapse; margin-bottom:16px;" width="100%" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td style="padding:10px; border:1px solid #e5e7eb; background:#f9fafb;">{{servicio_nombre}}</td>
                      <td style="padding:10px; border:1px solid #e5e7eb; text-align:right;">{{monto_total}}</td>
                    </tr>
                    <tr>
                      <td style="padding:10px; border:1px solid #e5e7eb; background:#f3f4f6; font-weight:bold;">
                        Total adeudado
                      </td>
                      <td style="padding:10px; border:1px solid #e5e7eb; text-align:right; font-weight:bold;">
                        {{monto_total}}
                      </td>
                    </tr>
                  </tbody>
                </table>

                <p style="font-size:14px; margin:0 0 16px 0; color:#111827;">
                  <strong>Fecha de eliminación definitiva:</strong> {{fecha_eliminacion}}
                </p>

                <h2 style="font-size:14px; margin:0 0 8px 0; color:#111827;">
                  Datos para realizar el pago
                </h2>

                <table style="border-collapse:separate; border-spacing:0; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; margin-bottom:16px;" width="100%" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td style="padding:12px; font-size:14px; line-height:1.7; color:#111827;">
                        <strong>Beneficiario:</strong> ' . e($companyName) . '<br />
                        <strong>RUT:</strong> ' . e($companyRut) . '<br />
                        <strong>Banco:</strong> ' . e($companyBank) . '<br />
                        <strong>Tipo de cuenta:</strong> ' . e($companyAccountType) . '<br />
                        <strong>Número de cuenta:</strong> ' . e($companyAccountNumber) . '<br />
                        <strong>Email de confirmación:</strong>
                        <a href="mailto:' . e($companyEmail) . '" style="color:#0ea5e9; text-decoration:none;">
                          ' . e($companyEmail) . '
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div style="text-align:center; margin-bottom:10px;">
                  <a href="{{link_pago}}"
                     style="background:' . e($accentColor) . '; color:#ffffff; padding:12px 18px; border-radius:10px;
                            text-decoration:none; font-size:14px; font-weight:bold; display:inline-block;">
                    Pagar factura #{{numero_factura}} · {{detalle_factura}} ({{monto_total}})
                  </a>
                </div>

                <div style="text-align:center; margin-bottom:10px;">
                  <a href="mailto:' . e($companyEmail) . '?subject=Comprobante%20de%20pago%20-%20{{dominio}}"
                     style="background:' . e($accentColor) . '; color:#ffffff; padding:12px 18px; border-radius:10px;
                            text-decoration:none; font-size:14px; font-weight:bold; display:inline-block;">
                    Enviar comprobante de pago
                  </a>
                </div>

                <p style="margin:0 0 6px 0; font-size:12px; line-height:1.6; color:#6b7280;">
                  Una vez realizado el pago, envíe el comprobante para confirmar y evitar la eliminación definitiva.
                </p>

                <hr style="border:none; border-top:1px solid #e5e7eb; margin:18px 0;" />

                <p style="font-size:12px; color:#6b7280; line-height:1.6; margin:0;">
                  Saludos cordiales,<br />
                  <strong>' . e($signature) . '</strong><br />
                  Área de Cobranza ·
                  <a href="mailto:' . e($companyEmail) . '" style="color:#6b7280; text-decoration:underline;">
                    ' . e($companyEmail) . '
                  </a>
                </p>

              </td>
            </tr>

            <tr>
              <td style="height:14px;">&nbsp;</td>
            </tr>

            <tr>
              <td style="font-size:11px; color:#9ca3af; line-height:1.6; padding:0 6px;">
                Nota: la eliminación del servicio puede implicar pérdida de información alojada y la interrupción total de los correos del dominio.
              </td>
            </tr>

          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>';

        return render_template_vars($html, $context);
    }

    private function renderTemplateOrFallback(string $name, string $fallback, array $context): string
    {
        $template = $this->db->fetch(
            'SELECT body_html FROM email_templates WHERE name = :name AND deleted_at IS NULL AND company_id = :company_id',
            ['name' => $name, 'company_id' => current_company_id()]
        );
        if (!$template) {
            return $fallback;
        }

        return render_template_vars($template['body_html'], $context);
    }

    private function getTemplateId(string $name): ?int
    {
        $template = $this->db->fetch(
            'SELECT id FROM email_templates WHERE name = :name AND deleted_at IS NULL AND company_id = :company_id',
            ['name' => $name, 'company_id' => current_company_id()]
        );

        return $template ? (int)$template['id'] : null;
    }

    private function processExpiredServices(int $companyId): void
    {
        $expiredServices = $this->db->fetchAll(
            'SELECT services.*
             FROM services
             WHERE services.company_id = :company_id
               AND services.deleted_at IS NULL
               AND services.due_date IS NOT NULL
               AND services.due_date < CURDATE()',
            ['company_id' => $companyId]
        );

        foreach ($expiredServices as $service) {
            if (($service['status'] ?? '') !== 'vencido') {
                $this->db->execute(
                    'UPDATE services SET status = :status, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
                    [
                        'status' => 'vencido',
                        'id' => $service['id'],
                        'company_id' => $companyId,
                    ]
                );
            }

            $existingRenewal = $this->db->fetch(
                'SELECT id FROM service_renewals
                 WHERE service_id = :service_id AND company_id = :company_id AND renewal_date = :renewal_date AND deleted_at IS NULL
                 ORDER BY id DESC LIMIT 1',
                [
                    'service_id' => $service['id'],
                    'company_id' => $companyId,
                    'renewal_date' => $service['due_date'],
                ]
            );

            if ($existingRenewal) {
                continue;
            }

            $this->db->execute(
                'INSERT INTO service_renewals (company_id, client_id, service_id, renewal_date, status, amount, currency, reminder_days, notes, created_at, updated_at)
                 VALUES (:company_id, :client_id, :service_id, :renewal_date, :status, :amount, :currency, :reminder_days, :notes, NOW(), NOW())',
                [
                    'company_id' => $companyId,
                    'client_id' => $service['client_id'],
                    'service_id' => $service['id'],
                    'renewal_date' => $service['due_date'],
                    'status' => 'pendiente_de_aprobacion',
                    'amount' => $service['cost'] ?? 0,
                    'currency' => $service['currency'] ?? 'CLP',
                    'reminder_days' => $service['notice_days_1'] ?? 15,
                    'notes' => 'Renovación generada automáticamente por vencimiento.',
                ]
            );
        }
    }
}
