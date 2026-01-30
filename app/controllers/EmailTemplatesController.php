<?php

class EmailTemplatesController extends Controller
{
    private EmailTemplatesModel $templates;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->templates = new EmailTemplatesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $templates = $this->templates->all('deleted_at IS NULL AND company_id = :company_id', [
            'company_id' => current_company_id(),
        ]);
        $this->render('email_templates/index', [
            'title' => 'Plantillas de Email',
            'pageTitle' => 'Plantillas de Email',
            'templates' => $templates,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->render('email_templates/create', [
            'title' => 'Nueva Plantilla',
            'pageTitle' => 'Nueva Plantilla',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $this->templates->create([
            'company_id' => current_company_id(),
            'name' => trim($_POST['name'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'body_html' => $_POST['body_html'] ?? '',
            'type' => $_POST['type'] ?? 'cobranza',
            'created_by' => Auth::user()['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'email_templates');
        flash('success', 'Plantilla creada correctamente.');
        $this->redirect('index.php?route=email-templates');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $template = $this->db->fetch(
            'SELECT * FROM email_templates WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$template) {
            $this->redirect('index.php?route=email-templates');
        }
        $clients = $this->db->fetchAll(
            'SELECT id, name, rut, email, billing_email FROM clients WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY name',
            ['company_id' => current_company_id()]
        );
        $this->render('email_templates/edit', [
            'title' => 'Editar Plantilla',
            'pageTitle' => 'Editar Plantilla',
            'template' => $template,
            'clients' => $clients,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $template = $this->db->fetch(
            'SELECT id FROM email_templates WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$template) {
            flash('error', 'Plantilla no encontrada para esta empresa.');
            $this->redirect('index.php?route=email-templates');
        }
        $this->templates->update($id, [
            'name' => trim($_POST['name'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'body_html' => $_POST['body_html'] ?? '',
            'type' => $_POST['type'] ?? 'cobranza',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'email_templates', $id);
        flash('success', 'Plantilla actualizada correctamente.');
        $this->redirect('index.php?route=email-templates');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $template = $this->db->fetch(
            'SELECT id FROM email_templates WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$template) {
            flash('error', 'Plantilla no encontrada para esta empresa.');
            $this->redirect('index.php?route=email-templates');
        }
        $this->templates->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'email_templates', $id);
        flash('success', 'Plantilla eliminada correctamente.');
        $this->redirect('index.php?route=email-templates');
    }

    public function seedDefaults(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = current_company_id();
        $templates = [
            [
                'name' => 'Informativa base',
                'subject' => 'Información de servicio',
                'type' => 'informativa',
                'file' => 'informativa.html',
            ],
            [
                'name' => 'Pago base',
                'subject' => 'Confirmación de pago',
                'type' => 'pago',
                'file' => 'pago.html',
            ],
            [
                'name' => 'Cobranza base',
                'subject' => 'Recordatorio de pago',
                'type' => 'cobranza',
                'file' => 'cobranza.html',
            ],
        ];
        $created = 0;
        foreach ($templates as $template) {
            $existing = $this->db->fetch(
                'SELECT id FROM email_templates WHERE name = :name AND deleted_at IS NULL AND company_id = :company_id',
                ['name' => $template['name'], 'company_id' => $companyId]
            );
            if ($existing) {
                continue;
            }
            $bodyHtml = $this->loadTemplateHtml($template['file']);
            if ($bodyHtml === null) {
                continue;
            }
            $this->templates->create([
                'company_id' => $companyId,
                'name' => $template['name'],
                'subject' => $template['subject'],
                'body_html' => $bodyHtml,
                'type' => $template['type'],
                'created_by' => Auth::user()['id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }

        flash('success', 'Plantillas cargadas: ' . $created);
        $this->redirect('index.php?route=email-templates');
    }

    public function preview(): void
    {
        $this->requireLogin();
        $templateId = (int)($_GET['template_id'] ?? 0);
        $clientId = (int)($_GET['client_id'] ?? 0);
        $template = $this->db->fetch(
            'SELECT * FROM email_templates WHERE id = :id AND company_id = :company_id',
            ['id' => $templateId, 'company_id' => current_company_id()]
        );
        $client = $clientId ? $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => current_company_id()]
        ) : null;

        if (!$template) {
            $this->redirect('index.php?route=email-templates');
        }

        $body = render_template_vars($template['body_html'], [
            'cliente_nombre' => $client['name'] ?? '',
            'rut' => $client['rut'] ?? '',
            'monto_total' => $client['balance'] ?? '',
            'fecha_vencimiento' => date('Y-m-d'),
            'servicio_nombre' => '',
        ]);

        $this->render('email_templates/preview', [
            'title' => 'Vista previa',
            'pageTitle' => 'Vista previa',
            'template' => $template,
            'client' => $client,
            'body' => $body,
        ]);
    }

    private function loadTemplateHtml(string $filename): ?string
    {
        $path = __DIR__ . '/../../storage/email_templates/' . $filename;
        if (!is_file($path)) {
            log_message('error', 'No se encontró la plantilla base: ' . $path);
            return null;
        }
        $contents = file_get_contents($path);
        if ($contents === false) {
            log_message('error', 'No se pudo leer la plantilla base: ' . $path);
            return null;
        }
        return $contents;
    }
}
