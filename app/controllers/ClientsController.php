<?php

class ClientsController extends Controller
{
    private ClientsModel $clients;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->clients = new ClientsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $clients = $this->clients->active(current_company_id());
        $this->render('clients/index', [
            'title' => 'Clientes',
            'pageTitle' => 'Clientes',
            'clients' => $clients,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->render('clients/create', [
            'title' => 'Nuevo Cliente',
            'pageTitle' => 'Nuevo Cliente',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (!Validator::required($name) || !Validator::email($email)) {
            flash('error', 'Completa los campos obligatorios.');
            $this->redirect('index.php?route=clients/create');
        }
        $billingEmail = $this->normalizeOptional($_POST['billing_email'] ?? null);
        if (!Validator::optionalEmail($billingEmail)) {
            flash('error', 'El email de cobranza no es válido.');
            $this->redirect('index.php?route=clients/create');
        }
        $mandanteEmail = $this->normalizeOptional($_POST['mandante_email'] ?? null);
        if (!Validator::optionalEmail($mandanteEmail)) {
            flash('error', 'El email del mandante no es válido.');
            $this->redirect('index.php?route=clients/create');
        }
        $rut = normalize_rut($_POST['rut'] ?? '');
        if (!Validator::rut($rut)) {
            flash('error', 'El RUT del cliente no es válido.');
            $this->redirect('index.php?route=clients/create');
        }
        $mandanteRut = normalize_rut($_POST['mandante_rut'] ?? '');
        if (!Validator::rut($mandanteRut)) {
            flash('error', 'El RUT del mandante no es válido.');
            $this->redirect('index.php?route=clients/create');
        }
        $companyId = current_company_id();
        $existingQuery = 'SELECT id FROM clients WHERE deleted_at IS NULL AND company_id = :company_id AND (email = :email';
        $existingParams = ['company_id' => $companyId, 'email' => $email];
        if ($rut !== '') {
            $existingQuery .= ' OR rut = :rut';
            $existingParams['rut'] = $rut;
        }
        $existingQuery .= ')';
        $existingClient = $this->db->fetch($existingQuery . ' LIMIT 1', $existingParams);
        if ($existingClient) {
            flash('error', 'Ya existe un cliente con este email o RUT. Revisa los datos antes de duplicar.');
            $this->redirect('index.php?route=clients/edit&id=' . $existingClient['id']);
        }

        $portalToken = bin2hex(random_bytes(16));
        $portalPassword = trim($_POST['portal_password'] ?? '');
        if ($portalPassword === '') {
            flash('error', 'Define una contraseña para el acceso del cliente.');
            $this->redirect('index.php?route=clients/create');
        }
        $avatarResult = upload_avatar($_FILES['avatar'] ?? null, 'client');
        if (!empty($avatarResult['error'])) {
            flash('error', $avatarResult['error']);
            $this->redirect('index.php?route=clients/create');
        }
        if ($billingEmail === '' && $email !== '') {
            $billingEmail = $email;
        }
        $data = [
            'company_id' => $companyId,
            'name' => $name,
            'rut' => $rut,
            'email' => $email,
            'billing_email' => $billingEmail,
            'phone' => $this->normalizeOptional($_POST['phone'] ?? null),
            'address' => $this->normalizeOptional($_POST['address'] ?? null),
            'giro' => $this->normalizeOptional($_POST['giro'] ?? null),
            'commune' => $this->normalizeOptional($_POST['commune'] ?? null),
            'contact' => $this->normalizeOptional($_POST['contact'] ?? null),
            'mandante_name' => $this->normalizeOptional($_POST['mandante_name'] ?? null),
            'mandante_rut' => $this->normalizeOptional($mandanteRut),
            'mandante_phone' => $this->normalizeOptional($_POST['mandante_phone'] ?? null),
            'mandante_email' => $mandanteEmail,
            'avatar_path' => $avatarResult['path'],
            'portal_token' => $portalToken,
            'portal_password' => password_hash($portalPassword, PASSWORD_DEFAULT),
            'notes' => $this->normalizeOptional($_POST['notes'] ?? null),
            'status' => $_POST['status'] ?? 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $clientId = $this->clients->create($data);
        create_notification(
            $this->db,
            $companyId,
            'Nuevo cliente',
            'Se creó el cliente "' . $name . '".',
            'success'
        );
        audit($this->db, Auth::user()['id'], 'create', 'clients');
        flash('success', 'Cliente creado correctamente.');
        $this->redirect('index.php?route=clients/edit&id=' . $clientId);
    }

    public function edit(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$client) {
            $this->redirect('index.php?route=clients');
        }
        $this->render('clients/edit', [
            'title' => 'Editar Cliente',
            'pageTitle' => 'Editar Cliente',
            'client' => $client,
            'portalUrl' => $this->buildPortalUrl($client),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $companyId = current_company_id();
        try {
            $client = $this->db->fetch(
                'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
                ['id' => $id, 'company_id' => $companyId]
            );
            if (!$client) {
                flash('error', 'Cliente no encontrado para esta empresa.');
                $this->redirect('index.php?route=clients');
            }
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            if (!Validator::required($name) || !Validator::email($email)) {
                flash('error', 'Completa los campos obligatorios.');
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }
            $billingEmail = $this->normalizeOptional($_POST['billing_email'] ?? null);
            if (!Validator::optionalEmail($billingEmail)) {
                flash('error', 'El email de cobranza no es válido.');
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }
            $mandanteEmail = $this->normalizeOptional($_POST['mandante_email'] ?? null);
            if (!Validator::optionalEmail($mandanteEmail)) {
                flash('error', 'El email del mandante no es válido.');
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }
            $rut = normalize_rut($_POST['rut'] ?? '');
            if (!Validator::rut($rut)) {
                flash('error', 'El RUT del cliente no es válido.');
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }
            $mandanteRut = normalize_rut($_POST['mandante_rut'] ?? '');
            if (!Validator::rut($mandanteRut)) {
                flash('error', 'El RUT del mandante no es válido.');
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }
            $existingQuery = 'SELECT id FROM clients WHERE deleted_at IS NULL AND company_id = :company_id AND id != :id AND (email = :email';
            $existingParams = ['company_id' => $companyId, 'id' => $id, 'email' => $email];
            if ($rut !== '') {
                $existingQuery .= ' OR rut = :rut';
                $existingParams['rut'] = $rut;
            }
            $existingQuery .= ')';
            $existingClient = $this->db->fetch($existingQuery . ' LIMIT 1', $existingParams);
            if ($existingClient) {
                flash('error', 'Ya existe un cliente con este email o RUT. Revisa los datos antes de guardar.');
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }

            $portalToken = trim($_POST['portal_token'] ?? '');
            if (!empty($_POST['regenerate_portal_token']) || $portalToken === '') {
                $portalToken = bin2hex(random_bytes(16));
            }
            $portalPassword = trim($_POST['portal_password'] ?? '');
            if ($billingEmail === '' && $email !== '') {
                $billingEmail = $email;
            }
            $data = [
                'name' => $name,
                'rut' => $rut,
                'email' => $email,
                'billing_email' => $billingEmail,
                'phone' => $this->normalizeOptional($_POST['phone'] ?? null),
                'address' => $this->normalizeOptional($_POST['address'] ?? null),
                'giro' => $this->normalizeOptional($_POST['giro'] ?? null),
                'commune' => $this->normalizeOptional($_POST['commune'] ?? null),
                'contact' => $this->normalizeOptional($_POST['contact'] ?? null),
                'mandante_name' => $this->normalizeOptional($_POST['mandante_name'] ?? null),
                'mandante_rut' => $this->normalizeOptional($mandanteRut),
                'mandante_phone' => $this->normalizeOptional($_POST['mandante_phone'] ?? null),
                'mandante_email' => $mandanteEmail,
                'portal_token' => $portalToken,
                'notes' => $this->normalizeOptional($_POST['notes'] ?? null),
                'status' => $_POST['status'] ?? 'activo',
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $avatarResult = upload_avatar($_FILES['avatar'] ?? null, 'client');
            if (!empty($avatarResult['error'])) {
                flash('error', $avatarResult['error']);
                $this->redirect('index.php?route=clients/edit&id=' . $id);
            }
            if (!empty($avatarResult['path'])) {
                $data['avatar_path'] = $avatarResult['path'];
            }
            if ($portalPassword !== '') {
                $data['portal_password'] = password_hash($portalPassword, PASSWORD_DEFAULT);
            }
            try {
                $this->clients->update($id, $data);
            } catch (PDOException $e) {
                if ($this->isPortalTokenCollision($e)) {
                    $data['portal_token'] = bin2hex(random_bytes(16));
                    $this->clients->update($id, $data);
                } else {
                    throw $e;
                }
            }
            audit($this->db, Auth::user()['id'], 'update', 'clients', $id);
            flash('success', 'Datos actualizados correctamente.');
            $this->redirect('index.php?route=clients/edit&id=' . $id);
        } catch (Throwable $e) {
            log_message('error', sprintf(
                'Error al actualizar cliente %s: %s | Payload: %s',
                $id,
                $e->getMessage(),
                json_encode($_POST, JSON_UNESCAPED_UNICODE)
            ));
            flash('error', 'No pudimos actualizar el cliente. Revisa los datos e intenta nuevamente.');
            $this->redirect('index.php?route=clients/edit&id=' . $id);
        }
    }

    private function isPortalTokenCollision(PDOException $e): bool
    {
        $sqlState = (string)($e->getCode() ?? '');
        $message = $e->getMessage();
        return $sqlState === '23000' && str_contains($message, 'idx_clients_portal_token');
    }

    private function normalizeOptional(?string $value): ?string
    {
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    public function show(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$client) {
            $this->redirect('index.php?route=clients');
        }
        $companyId = current_company_id();
        $services = $this->db->fetchAll('SELECT * FROM services WHERE client_id = :id AND company_id = :company_id AND deleted_at IS NULL', ['id' => $id, 'company_id' => $companyId]);
        $projects = $this->db->fetchAll('SELECT * FROM projects WHERE client_id = :id AND company_id = :company_id AND deleted_at IS NULL', ['id' => $id, 'company_id' => $companyId]);
        $invoices = $this->db->fetchAll('SELECT * FROM invoices WHERE client_id = :id AND company_id = :company_id AND deleted_at IS NULL', ['id' => $id, 'company_id' => $companyId]);
        $emails = $this->db->fetchAll('SELECT * FROM email_logs WHERE client_id = :id AND company_id = :company_id ORDER BY created_at DESC', ['id' => $id, 'company_id' => $companyId]);
        $payments = $this->db->fetchAll(
            'SELECT payments.* FROM payments JOIN invoices ON payments.invoice_id = invoices.id WHERE invoices.client_id = :id AND invoices.company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );

        $this->render('clients/show', [
            'title' => 'Detalle Cliente',
            'pageTitle' => 'Detalle Cliente',
            'client' => $client,
            'services' => $services,
            'projects' => $projects,
            'invoices' => $invoices,
            'emails' => $emails,
            'payments' => $payments,
            'portalUrl' => $this->buildPortalUrl($client),
        ]);
    }

    public function history(): void
    {
        $this->requireLogin();
        $clientId = (int)($_GET['id'] ?? 0);
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $clients = $this->clients->active($companyId);
        $client = null;
        if ($clientId > 0) {
            $client = $this->db->fetch(
                'SELECT * FROM clients WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
                ['id' => $clientId, 'company_id' => $companyId]
            );
            if (!$client) {
                flash('error', 'Cliente no encontrado para esta empresa.');
                $this->redirect('index.php?route=clients');
            }
        }

        $filterClause = $clientId > 0 ? ' AND {table}.client_id = :client_id' : '';
        $filterParams = $clientId > 0 ? ['client_id' => $clientId] : [];

        $activities = [];
        if ($client) {
            $sources = [
                [
                    'table' => 'projects',
                    'sql' => 'SELECT projects.id, projects.name, projects.status, projects.created_at, projects.updated_at, clients.name as client_name
                     FROM projects
                     JOIN clients ON projects.client_id = clients.id
                     WHERE projects.company_id = :company_id AND projects.deleted_at IS NULL' . $filterClause . '
                     ORDER BY projects.id DESC
                     LIMIT 50',
                    'map' => static function (array $row): array {
                        $projectId = (int)($row['id'] ?? 0);
                        return [
                            'type' => 'Proyecto',
                            'title' => $row['name'] ?? '',
                            'status' => $row['status'] ?? '',
                            'client' => $row['client_name'] ?? '',
                            'date' => $row['updated_at'] ?? $row['created_at'] ?? '',
                            'url' => 'index.php?route=projects/show&id=' . $projectId,
                            'actions' => [
                                ['label' => 'Ver', 'url' => 'index.php?route=projects/show&id=' . $projectId],
                                ['label' => 'Editar', 'url' => 'index.php?route=projects/edit&id=' . $projectId],
                            ],
                        ];
                    },
                ],
                [
                    'table' => 'services',
                    'sql' => 'SELECT services.id, services.name, services.service_type, services.status, services.created_at, services.updated_at, clients.name as client_name
                     FROM services
                     JOIN clients ON services.client_id = clients.id
                     WHERE services.company_id = :company_id AND services.deleted_at IS NULL' . $filterClause . '
                     ORDER BY services.id DESC
                     LIMIT 50',
                    'map' => static function (array $row): array {
                        $serviceId = (int)($row['id'] ?? 0);
                        return [
                            'type' => 'Servicio',
                            'title' => $row['name'] ?? '',
                            'status' => $row['status'] ?? '',
                            'client' => $row['client_name'] ?? '',
                            'date' => $row['updated_at'] ?? $row['created_at'] ?? '',
                            'url' => 'index.php?route=services/show&id=' . $serviceId,
                            'meta' => $row['service_type'] ?? '',
                            'actions' => [
                                ['label' => 'Ver', 'url' => 'index.php?route=services/show&id=' . $serviceId],
                                ['label' => 'Editar', 'url' => 'index.php?route=services/edit&id=' . $serviceId],
                            ],
                        ];
                    },
                ],
                [
                    'table' => 'support_tickets',
                    'sql' => 'SELECT support_tickets.id, support_tickets.subject, support_tickets.status, support_tickets.priority, support_tickets.created_at, support_tickets.updated_at, clients.name as client_name
                     FROM support_tickets
                     JOIN clients ON support_tickets.client_id = clients.id
                     WHERE support_tickets.company_id = :company_id AND support_tickets.deleted_at IS NULL' . $filterClause . '
                     ORDER BY support_tickets.id DESC
                     LIMIT 50',
                    'map' => static function (array $row): array {
                        $ticketId = (int)($row['id'] ?? 0);
                        return [
                            'type' => 'Ticket',
                            'title' => $row['subject'] ?? '',
                            'status' => $row['status'] ?? '',
                            'client' => $row['client_name'] ?? '',
                            'date' => $row['updated_at'] ?? $row['created_at'] ?? '',
                            'url' => 'index.php?route=tickets/show&id=' . $ticketId,
                            'meta' => $row['priority'] ?? '',
                            'actions' => [
                                ['label' => 'Ver', 'url' => 'index.php?route=tickets/show&id=' . $ticketId],
                                ['label' => 'Editar', 'url' => 'index.php?route=tickets/edit&id=' . $ticketId],
                            ],
                        ];
                    },
                ],
                [
                    'table' => 'invoices',
                    'sql' => 'SELECT invoices.id, invoices.numero, invoices.estado, invoices.fecha_emision, invoices.created_at, clients.name as client_name
                     FROM invoices
                     JOIN clients ON invoices.client_id = clients.id
                     WHERE invoices.company_id = :company_id AND invoices.deleted_at IS NULL' . $filterClause . '
                     ORDER BY invoices.id DESC
                     LIMIT 50',
                    'map' => static function (array $row): array {
                        $invoiceId = (int)($row['id'] ?? 0);
                        return [
                            'type' => 'Factura',
                            'title' => 'Factura #' . ($row['numero'] ?? $row['id'] ?? ''),
                            'status' => $row['estado'] ?? '',
                            'client' => $row['client_name'] ?? '',
                            'date' => $row['created_at'] ?? $row['fecha_emision'] ?? '',
                            'url' => 'index.php?route=invoices/show&id=' . $invoiceId,
                            'actions' => [
                                ['label' => 'Ver', 'url' => 'index.php?route=invoices/show&id=' . $invoiceId],
                                ['label' => 'Editar', 'url' => 'index.php?route=invoices/edit&id=' . $invoiceId],
                            ],
                        ];
                    },
                ],
                [
                    'table' => 'service_renewals',
                    'sql' => 'SELECT service_renewals.id, service_renewals.renewal_date, service_renewals.status, service_renewals.amount, service_renewals.currency, service_renewals.created_at, clients.name as client_name
                     FROM service_renewals
                     JOIN clients ON service_renewals.client_id = clients.id
                     WHERE service_renewals.company_id = :company_id AND service_renewals.deleted_at IS NULL' . $filterClause . '
                     ORDER BY service_renewals.renewal_date DESC, service_renewals.id DESC
                     LIMIT 50',
                    'map' => static function (array $row): array {
                        $renewalId = (int)($row['id'] ?? 0);
                        return [
                            'type' => 'Renovación',
                            'title' => 'Renovación programada',
                            'status' => $row['status'] ?? '',
                            'client' => $row['client_name'] ?? '',
                            'date' => $row['created_at'] ?? ($row['renewal_date'] . ' 00:00:00'),
                            'meta' => format_currency((float)($row['amount'] ?? 0)) . ' ' . ($row['currency'] ?? ''),
                            'url' => 'index.php?route=crm/renewals',
                            'actions' => [
                                ['label' => 'Ver', 'url' => 'index.php?route=crm/renewals'],
                                ['label' => 'Editar', 'url' => 'index.php?route=crm/renewals&focus=' . $renewalId],
                            ],
                        ];
                    },
                ],
            ];

            foreach ($sources as $source) {
                try {
                    $rows = $this->db->fetchAll(
                        str_replace('{table}', $source['table'], $source['sql']),
                        array_merge(['company_id' => $companyId], $filterParams)
                    );
                    foreach ($rows as $row) {
                        $activities[] = $source['map']($row);
                    }
                } catch (Throwable $e) {
                    log_message('error', 'Failed to load client history for ' . $source['table'] . ': ' . $e->getMessage());
                }
            }

            usort($activities, static function (array $a, array $b): int {
                return strtotime((string)($b['date'] ?? '')) <=> strtotime((string)($a['date'] ?? ''));
            });
        }

        $this->render('clients/history', [
            'title' => 'Historial del Cliente',
            'pageTitle' => 'Historial del Cliente',
            'client' => $client,
            'clients' => $clients,
            'activities' => $activities,
            'selectedClientId' => $clientId,
        ]);
    }

    public function lookup(): void
    {
        $this->requireLogin();
        $term = trim($_GET['term'] ?? '');
        header('Content-Type: application/json; charset=utf-8');
        if ($term === '') {
            echo json_encode(['found' => false], JSON_UNESCAPED_UNICODE);
            return;
        }
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE company_id = :company_id AND deleted_at IS NULL AND (email = :term OR rut = :term) ORDER BY id DESC LIMIT 1',
            ['company_id' => current_company_id(), 'term' => $term]
        );
        if (!$client) {
            echo json_encode(['found' => false], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode([
            'found' => true,
            'client' => [
                'id' => $client['id'] ?? null,
                'name' => $client['name'] ?? '',
                'rut' => $client['rut'] ?? '',
                'email' => $client['email'] ?? '',
                'billing_email' => $client['billing_email'] ?? '',
                'phone' => $client['phone'] ?? '',
                'contact' => $client['contact'] ?? '',
                'mandante_name' => $client['mandante_name'] ?? '',
                'mandante_rut' => $client['mandante_rut'] ?? '',
                'mandante_phone' => $client['mandante_phone'] ?? '',
                'mandante_email' => $client['mandante_email'] ?? '',
                'address' => $client['address'] ?? '',
                'giro' => $client['giro'] ?? '',
                'commune' => $client['commune'] ?? '',
                'status' => $client['status'] ?? 'activo',
                'notes' => $client['notes'] ?? '',
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    public function portalLogin(): void
    {
        $error = null;
        $email = '';
        $companyId = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $companyId = (int)($_POST['company_id'] ?? 0);
            if (!Validator::email($email) || $password === '') {
                $error = 'Completa los datos solicitados.';
            } elseif ($companyId === 0) {
                $error = 'Selecciona una empresa.';
            } else {
                $client = $this->db->fetch(
                    'SELECT * FROM clients WHERE email = :email AND company_id = :company_id AND deleted_at IS NULL',
                    [
                        'email' => $email,
                        'company_id' => $companyId,
                    ]
                );
                if (!$client || empty($client['portal_password'])) {
                    $error = 'Las credenciales no son válidas.';
                } else {
                    $storedPassword = (string)$client['portal_password'];
                    $passwordMatches = password_verify($password, $storedPassword);
                    if (!$passwordMatches && hash_equals($storedPassword, $password)) {
                        $passwordMatches = true;
                        $this->clients->update((int)$client['id'], [
                            'portal_password' => password_hash($password, PASSWORD_DEFAULT),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    if (!$passwordMatches) {
                        $error = 'Las credenciales no son válidas.';
                    } else {
                        $_SESSION['client_portal_token'] = $client['portal_token'];
                        $_SESSION['client_company_id'] = $client['company_id'] ?? null;
                        $this->redirect('index.php?route=clients/portal&token=' . urlencode($client['portal_token']));
                    }
                }
            }
        }

        $this->renderPublic('clients/login', [
            'title' => 'Acceso Intranet Cliente',
            'pageTitle' => 'Acceso Portal Cliente',
            'error' => $error,
            'email' => $email,
            'companyId' => $companyId,
            'companies' => (new CompaniesModel($this->db))->active(),
            'showAdminAccess' => true,
            'hidePortalHeader' => true,
        ]);
    }

    public function portal(): void
    {
        $sessionToken = $_SESSION['client_portal_token'] ?? '';
        $token = trim($_GET['token'] ?? $sessionToken);
        if ($token === '' || ($sessionToken !== '' && $token !== $sessionToken)) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder al portal.';
            $this->redirect('index.php?route=clients/login');
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            $_SESSION['error'] = 'No encontramos un cliente asociado a este acceso.';
            $this->redirect('index.php?route=clients/login');
        }

        $activities = $this->db->fetchAll(
            'SELECT project_tasks.*, projects.name as project_name
             FROM project_tasks
             JOIN projects ON project_tasks.project_id = projects.id
             WHERE projects.client_id = :id AND projects.deleted_at IS NULL
             ORDER BY project_tasks.created_at DESC',
            ['id' => $client['id']]
        );
        $payments = $this->db->fetchAll(
            'SELECT payments.*, invoices.numero as invoice_number, invoices.estado as invoice_status, invoices.total as invoice_total
             FROM payments
             JOIN invoices ON payments.invoice_id = invoices.id
             WHERE invoices.client_id = :id
             ORDER BY payments.fecha_pago DESC',
            ['id' => $client['id']]
        );
        $pendingInvoices = $this->db->fetchAll(
            'SELECT * FROM invoices WHERE client_id = :id AND estado != "pagado" AND deleted_at IS NULL ORDER BY fecha_vencimiento ASC',
            ['id' => $client['id']]
        );
        $pendingTotal = array_sum(array_map(static fn(array $invoice) => (float)$invoice['total'], $pendingInvoices));
        $paidTotal = array_sum(array_map(static fn(array $payment) => (float)$payment['monto'], $payments));
        $projectsOverview = $this->db->fetchAll(
            'SELECT projects.*,
                COUNT(project_tasks.id) as tasks_total,
                COALESCE(SUM(CASE WHEN project_tasks.progress_percent >= 100 THEN 1 ELSE 0 END), 0) as tasks_completed,
                COALESCE(SUM(project_tasks.progress_percent), 0) as tasks_progress,
                MAX(project_tasks.created_at) as last_activity
             FROM projects
             LEFT JOIN project_tasks ON project_tasks.project_id = projects.id
             WHERE projects.client_id = :id AND projects.deleted_at IS NULL
             GROUP BY projects.id
             ORDER BY projects.created_at DESC',
            ['id' => $client['id']]
        );
        $projectTasks = $this->db->fetchAll(
            'SELECT project_tasks.*, projects.name as project_name
             FROM project_tasks
             JOIN projects ON project_tasks.project_id = projects.id
             WHERE projects.client_id = :id AND projects.deleted_at IS NULL
             ORDER BY COALESCE(project_tasks.start_date, project_tasks.created_at) ASC',
            ['id' => $client['id']]
        );

        $ticketModel = new SupportTicketsModel($this->db);
        $ticketMessageModel = new SupportTicketMessagesModel($this->db);
        $supportTickets = $ticketModel->forClient((int)$client['id']);
        $activeSupportTicketId = (int)($_GET['ticket'] ?? 0);
        if ($activeSupportTicketId === 0 && !empty($supportTickets)) {
            $activeSupportTicketId = (int)$supportTickets[0]['id'];
        }
        $activeSupportTicket = null;
        $supportMessages = [];
        if ($activeSupportTicketId !== 0) {
            $activeSupportTicket = $this->db->fetch(
                'SELECT * FROM support_tickets WHERE id = :id AND client_id = :client_id',
                ['id' => $activeSupportTicketId, 'client_id' => $client['id']]
            );
            if ($activeSupportTicket) {
                $supportMessages = $ticketMessageModel->forTicket($activeSupportTicketId);
            }
        }

        $this->renderPublic('clients/portal', [
            'title' => 'Portal Cliente',
            'pageTitle' => 'Portal Cliente',
            'client' => $client,
            'activities' => $activities,
            'payments' => $payments,
            'pendingInvoices' => $pendingInvoices,
            'pendingTotal' => $pendingTotal,
            'paidTotal' => $paidTotal,
            'projectsOverview' => $projectsOverview,
            'projectTasks' => $projectTasks,
            'supportTickets' => $supportTickets,
            'activeSupportTicket' => $activeSupportTicket,
            'activeSupportTicketId' => $activeSupportTicketId,
            'supportMessages' => $supportMessages,
            'supportSuccess' => $_SESSION['support_success'] ?? null,
            'supportError' => $_SESSION['support_error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);
        unset($_SESSION['success']);
        unset($_SESSION['support_success'], $_SESSION['support_error']);
    }

    public function portalLogout(): void
    {
        unset($_SESSION['client_portal_token']);
        unset($_SESSION['client_company_id']);
        $this->redirect('index.php?route=clients/login');
    }

    public function portalInvoice(): void
    {
        $sessionToken = $_SESSION['client_portal_token'] ?? '';
        $token = trim($_GET['token'] ?? $sessionToken);
        if ($token === '' || ($sessionToken !== '' && $token !== $sessionToken)) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder al portal.';
            $this->redirect('index.php?route=clients/login');
        }

        $invoiceId = (int)($_GET['id'] ?? 0);
        $invoice = $this->db->fetch('SELECT * FROM invoices WHERE id = :id AND deleted_at IS NULL', ['id' => $invoiceId]);
        if (!$invoice) {
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token));
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client || (int)$invoice['client_id'] !== (int)$client['id']) {
            $this->redirect('index.php?route=clients/login');
        }

        $items = $this->db->fetchAll('SELECT * FROM invoice_items WHERE invoice_id = :invoice_id', ['invoice_id' => $invoiceId]);
        $settings = new SettingsModel($this->db);
        $company = $settings->get('company', []);

        $this->renderPublic('clients/invoice', [
            'title' => 'Detalle Factura',
            'pageTitle' => 'Detalle Factura',
            'invoice' => $invoice,
            'client' => $client,
            'items' => $items,
            'company' => $company,
        ]);
    }

    private function buildPortalUrl(array $client): string
    {
        $baseUrl = rtrim($this->config['app']['base_url'] ?? '', '/');
        $token = $client['portal_token'] ?? '';
        if ($token === '') {
            return '';
        }
        $path = 'index.php?route=clients/login';
        return $baseUrl !== '' ? $baseUrl . '/' . $path : $path;
    }

    public function portalUpdate(): void
    {
        verify_csrf();
        $token = trim($_POST['token'] ?? '');
        if ($token === '' || empty($_SESSION['client_portal_token']) || $token !== $_SESSION['client_portal_token']) {
            $this->redirect('index.php?route=clients/login');
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            $this->redirect('index.php?route=clients/login');
        }

        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $contact = trim($_POST['contact'] ?? '');

        if (!Validator::email($email)) {
            $_SESSION['error'] = 'Ingresa un correo válido.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token));
        }

        $avatarResult = upload_avatar($_FILES['avatar'] ?? null, 'client');
        if (!empty($avatarResult['error'])) {
            $_SESSION['error'] = $avatarResult['error'];
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token));
        }
        $data = [
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'contact' => $contact,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if (!empty($avatarResult['path'])) {
            $data['avatar_path'] = $avatarResult['path'];
        }

        $this->clients->update((int)$client['id'], $data);

        $_SESSION['success'] = 'Perfil actualizado correctamente.';
        $this->redirect('index.php?route=clients/portal&token=' . urlencode($token));
    }

    public function portalChatMessages(): void
    {
        $sessionToken = $_SESSION['client_portal_token'] ?? '';
        $token = trim($_GET['token'] ?? $sessionToken);
        if ($token === '' || ($sessionToken !== '' && $token !== $sessionToken)) {
            http_response_code(403);
            echo json_encode(['messages' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            echo json_encode(['messages' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        $threadId = (int)($_GET['thread'] ?? 0);
        if ($threadId === 0) {
            echo json_encode(['messages' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        $chatModel = new ChatModel($this->db);
        $thread = $chatModel->getThreadForClient($threadId, (int)$client['id']);
        if (!$thread) {
            echo json_encode(['messages' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        $sinceId = (int)($_GET['since'] ?? 0);
        $messages = $sinceId > 0
            ? $chatModel->getMessagesSince($threadId, $sinceId)
            : $chatModel->getMessages($threadId);

        echo json_encode(['messages' => $messages], JSON_UNESCAPED_UNICODE);
    }

    public function portalChatNotifications(): void
    {
        $sessionToken = $_SESSION['client_portal_token'] ?? '';
        $token = trim($_GET['token'] ?? $sessionToken);
        if ($token === '' || ($sessionToken !== '' && $token !== $sessionToken)) {
            http_response_code(403);
            echo json_encode(['latest_id' => 0], JSON_UNESCAPED_UNICODE);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            echo json_encode(['latest_id' => 0], JSON_UNESCAPED_UNICODE);
            return;
        }

        $chatModel = new ChatModel($this->db);
        $latestId = $chatModel->getLatestMessageIdForClient((int)$client['id']);
        echo json_encode(['latest_id' => $latestId], JSON_UNESCAPED_UNICODE);
    }

    public function portalChatCreate(): void
    {
        verify_csrf();
        $token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
        if ($token === '' || empty($_SESSION['client_portal_token']) || $token !== $_SESSION['client_portal_token']) {
            $this->redirect('index.php?route=clients/login');
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            $this->redirect('index.php?route=clients/login');
        }

        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($subject === '' || $message === '') {
            $_SESSION['chat_error'] = 'Completa el asunto y el mensaje para iniciar la conversación.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '#portal-chat');
        }

        $chatModel = new ChatModel($this->db);
        $threadId = $chatModel->createThread((int)$client['id'], $subject);
        $chatModel->addMessage($threadId, 'client', (int)$client['id'], $message);
        $_SESSION['chat_success'] = 'Conversación creada correctamente.';
        $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '&thread=' . $threadId . '#portal-chat');
    }

    public function portalChatSend(): void
    {
        verify_csrf();
        $token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
        if ($token === '' || empty($_SESSION['client_portal_token']) || $token !== $_SESSION['client_portal_token']) {
            $this->redirect('index.php?route=clients/login');
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            $this->redirect('index.php?route=clients/login');
        }

        $threadId = (int)($_POST['thread_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($threadId === 0 || $message === '') {
            $_SESSION['chat_error'] = 'Escribe un mensaje antes de enviar.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '#portal-chat');
        }

        $chatModel = new ChatModel($this->db);
        $thread = $chatModel->getThreadForClient($threadId, (int)$client['id']);
        if (!$thread) {
            $_SESSION['chat_error'] = 'No encontramos la conversación seleccionada.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '#portal-chat');
        }

        $chatModel->addMessage($threadId, 'client', (int)$client['id'], $message);
        $_SESSION['chat_success'] = 'Mensaje enviado correctamente.';
        $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '&thread=' . $threadId . '#portal-chat');
    }

    public function portalTicketCreate(): void
    {
        verify_csrf();
        $token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
        if ($token === '' || empty($_SESSION['client_portal_token']) || $token !== $_SESSION['client_portal_token']) {
            $this->redirect('index.php?route=clients/login');
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            $this->redirect('index.php?route=clients/login');
        }

        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $priority = $_POST['priority'] ?? 'media';
        if ($subject === '' || $message === '') {
            $_SESSION['support_error'] = 'Completa el asunto y el mensaje.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '#portal-support');
        }

        $ticketModel = new SupportTicketsModel($this->db);
        $messageModel = new SupportTicketMessagesModel($this->db);
        $now = date('Y-m-d H:i:s');
        $ticketId = $ticketModel->create([
            'client_id' => (int)$client['id'],
            'subject' => $subject,
            'description' => $message,
            'status' => 'abierto',
            'priority' => $priority,
            'assigned_user_id' => null,
            'created_by_type' => 'client',
            'created_by_id' => (int)$client['id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $messageModel->create([
            'ticket_id' => $ticketId,
            'sender_type' => 'client',
            'sender_id' => (int)$client['id'],
            'message' => $message,
            'created_at' => $now,
        ]);
        $_SESSION['support_success'] = 'Ticket creado correctamente.';
        $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '&ticket=' . $ticketId . '#portal-support');
    }

    public function portalTicketMessage(): void
    {
        verify_csrf();
        $token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
        if ($token === '' || empty($_SESSION['client_portal_token']) || $token !== $_SESSION['client_portal_token']) {
            $this->redirect('index.php?route=clients/login');
        }

        $client = $this->db->fetch('SELECT * FROM clients WHERE portal_token = :token AND deleted_at IS NULL', ['token' => $token]);
        if (!$client) {
            $this->redirect('index.php?route=clients/login');
        }

        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($ticketId === 0 || $message === '') {
            $_SESSION['support_error'] = 'Escribe un mensaje antes de enviar.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '#portal-support');
        }

        $ticket = $this->db->fetch(
            'SELECT * FROM support_tickets WHERE id = :id AND client_id = :client_id',
            ['id' => $ticketId, 'client_id' => $client['id']]
        );
        if (!$ticket) {
            $_SESSION['support_error'] = 'No encontramos el ticket seleccionado.';
            $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '#portal-support');
        }

        $messageModel = new SupportTicketMessagesModel($this->db);
        $now = date('Y-m-d H:i:s');
        $messageModel->create([
            'ticket_id' => $ticketId,
            'sender_type' => 'client',
            'sender_id' => (int)$client['id'],
            'message' => $message,
            'created_at' => $now,
        ]);
        $ticketModel = new SupportTicketsModel($this->db);
        $ticketModel->update($ticketId, ['updated_at' => $now]);
        $_SESSION['support_success'] = 'Respuesta enviada.';
        $this->redirect('index.php?route=clients/portal&token=' . urlencode($token) . '&ticket=' . $ticketId . '#portal-support');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $companyId = current_company_id();
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=clients');
        }
        $linkedCounts = [
            'facturas' => $this->db->fetch('SELECT COUNT(*) as total FROM invoices WHERE client_id = :id AND deleted_at IS NULL', ['id' => $id]),
            'proyectos' => $this->db->fetch('SELECT COUNT(*) as total FROM projects WHERE client_id = :id AND deleted_at IS NULL', ['id' => $id]),
            'servicios' => $this->db->fetch('SELECT COUNT(*) as total FROM services WHERE client_id = :id AND deleted_at IS NULL', ['id' => $id]),
            'cotizaciones' => $this->db->fetch('SELECT COUNT(*) as total FROM quotes WHERE client_id = :id', ['id' => $id]),
            'tickets' => $this->db->fetch('SELECT COUNT(*) as total FROM support_tickets WHERE client_id = :id', ['id' => $id]),
            'pagos' => $this->db->fetch(
                'SELECT COUNT(*) as total
                 FROM payments
                 JOIN invoices ON payments.invoice_id = invoices.id
                 WHERE invoices.client_id = :id AND invoices.deleted_at IS NULL',
                ['id' => $id]
            ),
        ];
        $blocked = [];
        foreach ($linkedCounts as $label => $row) {
            if (!empty($row['total'])) {
                $blocked[] = $label;
            }
        }
        if (!empty($blocked)) {
            flash('error', 'No se puede eliminar el cliente porque tiene registros asociados: ' . implode(', ', $blocked) . '.');
            $this->redirect('index.php?route=clients');
        }
        $this->clients->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'clients', $id);
        flash('success', 'Cliente eliminado correctamente.');
        $this->redirect('index.php?route=clients');
    }
}
