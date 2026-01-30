<?php

class EmailConfigController extends Controller
{
    private SettingsModel $settings;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->settings = new SettingsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $smtpConfig = $this->settings->get('smtp_info', []);
        if (!is_array($smtpConfig)) {
            $smtpConfig = [];
        }
        $defaults = [
            'host' => 'ges.gocreative.cl',
            'port_ssl' => 465,
            'port_tls' => 587,
            'security' => 'ssl',
            'username' => 'info@ges.gocreative.cl',
            'password' => '',
            'from_name' => 'Información',
            'from_email' => 'info@ges.gocreative.cl',
            'reply_to' => 'info@ges.gocreative.cl',
            'incoming_host' => 'ges.gocreative.cl',
            'imap_port' => 993,
            'pop3_port' => 995,
        ];
        $smtpConfig = array_merge($defaults, $smtpConfig);
        $this->render('maintainers/email-config', [
            'title' => 'Configuración de correo',
            'pageTitle' => 'Configuración de correo',
            'smtpConfig' => $smtpConfig,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $this->settings->set('smtp_info', [
            'host' => trim($_POST['host'] ?? ''),
            'port_ssl' => (int)($_POST['port_ssl'] ?? 465),
            'port_tls' => (int)($_POST['port_tls'] ?? 587),
            'security' => trim($_POST['security'] ?? 'tls'),
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'from_name' => trim($_POST['from_name'] ?? ''),
            'from_email' => trim($_POST['from_email'] ?? ''),
            'reply_to' => trim($_POST['reply_to'] ?? ''),
            'incoming_host' => trim($_POST['incoming_host'] ?? ''),
            'imap_port' => (int)($_POST['imap_port'] ?? 993),
            'pop3_port' => (int)($_POST['pop3_port'] ?? 995),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'smtp_info');
        flash('success', 'Configuración SMTP actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/email-config');
    }

    public function test(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $smtpConfig = $this->settings->get('smtp_info', []);
        if (!is_array($smtpConfig)) {
            $smtpConfig = [];
        }
        $hasRequiredConfig = !empty($smtpConfig['host']) && !empty($smtpConfig['username']) && !empty($smtpConfig['password']);
        if (!$hasRequiredConfig) {
            flash('error', 'Completa la configuración SMTP antes de probar.');
            $this->redirect('index.php?route=maintainers/email-config&test=missing-config');
        }
        $to = Auth::user()['email'] ?? '';
        if ($to === '') {
            $company = $this->settings->get('company', []);
            $to = $company['email'] ?? '';
        }
        if ($to === '') {
            $this->db->execute('INSERT INTO notifications (company_id, title, message, type, created_at, updated_at) VALUES (:company_id, :title, :message, :type, NOW(), NOW())', [
                'company_id' => current_company_id(),
                'title' => 'Prueba SMTP',
                'message' => 'No se encontró correo para enviar la prueba.',
                'type' => 'danger',
            ]);
            flash('error', 'No se encontró correo para enviar la prueba.');
            $this->redirect('index.php?route=maintainers/email-config&test=missing');
        }

        $mailer = new Mailer($this->db);
        $sent = $mailer->send('info', $to, 'Prueba SMTP', '<p>Correo de prueba exitoso.</p>');

        $this->db->execute('INSERT INTO notifications (company_id, title, message, type, created_at, updated_at) VALUES (:company_id, :title, :message, :type, NOW(), NOW())', [
            'company_id' => current_company_id(),
            'title' => 'Prueba SMTP',
            'message' => $sent ? 'Correo enviado correctamente.' : 'Fallo el envío.',
            'type' => $sent ? 'success' : 'danger',
        ]);
        flash($sent ? 'success' : 'error', $sent ? 'Correo de prueba enviado correctamente.' : 'Fallo el envío del correo de prueba.');

        $this->redirect('index.php?route=maintainers/email-config&test=' . ($sent ? 'success' : 'failed'));
    }
}
