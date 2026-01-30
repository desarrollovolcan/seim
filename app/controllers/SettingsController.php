<?php

class SettingsController extends Controller
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
        $company = $this->settings->get('company', []);
        $billing = $this->settings->get('billing_defaults', []);
        $invoiceDefaults = $this->settings->get('invoice_defaults', []);
        $currencyFormat = $this->settings->get('currency_format', $this->config['currency_format'] ?? []);
        $communeCityMap = chile_commune_city_map($this->db);
        $communes = array_keys($communeCityMap);
        $activityCodeOptions = sii_activity_code_options($this->db);
        $this->render('settings/index', [
            'title' => 'Configuración',
            'pageTitle' => 'Configuración',
            'company' => $company,
            'billing' => $billing,
            'invoiceDefaults' => $invoiceDefaults,
            'currencyFormat' => $currencyFormat,
            'communes' => $communes,
            'communeCityMap' => $communeCityMap,
            'activityCodeOptions' => $activityCodeOptions,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = current_company_id();
        $section = $_POST['section'] ?? '';
        if ($section === 'company') {
            $company = $this->settings->get('company', []);
            $logoColorResult = upload_company_logo($_FILES['logo_color'] ?? null, 'logo-color');
            if (!empty($logoColorResult['error'])) {
                flash('error', $logoColorResult['error']);
                $this->redirect('index.php?route=settings');
            }
            $logoBlackResult = upload_company_logo($_FILES['logo_black'] ?? null, 'logo-black');
            if (!empty($logoBlackResult['error'])) {
                flash('error', $logoBlackResult['error']);
                $this->redirect('index.php?route=settings');
            }
            $loginLogoResult = upload_company_logo($_FILES['login_logo'] ?? null, 'logo-login');
            if (!empty($loginLogoResult['error'])) {
                flash('error', $loginLogoResult['error']);
                $this->redirect('index.php?route=settings');
            }
            $companyData = [
                'name' => trim($_POST['name'] ?? ''),
                'rut' => trim($_POST['rut'] ?? ''),
                'bank' => trim($_POST['bank'] ?? ''),
                'account_type' => trim($_POST['account_type'] ?? ''),
                'account_number' => trim($_POST['account_number'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'giro' => trim($_POST['giro'] ?? ''),
                'activity_code' => trim($_POST['activity_code'] ?? ''),
                'commune' => trim($_POST['commune'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'signature' => trim($_POST['signature'] ?? ''),
                'logo_color' => $company['logo_color'] ?? null,
                'logo_black' => $company['logo_black'] ?? null,
                'login_logo' => $company['login_logo'] ?? null,
            ];
            if (!empty($logoColorResult['path'])) {
                $companyData['logo_color'] = $logoColorResult['path'];
            }
            if (!empty($logoBlackResult['path'])) {
                $companyData['logo_black'] = $logoBlackResult['path'];
            }
            if (!empty($loginLogoResult['path'])) {
                $companyData['login_logo'] = $loginLogoResult['path'];
            }
            $this->settings->set('company', $companyData);
            if ($companyId) {
                $companies = new CompaniesModel($this->db);
                $companies->update($companyId, [
                    'name' => $companyData['name'],
                    'rut' => $companyData['rut'],
                    'email' => $companyData['email'],
                    'phone' => $companyData['phone'],
                    'address' => $companyData['address'],
                    'giro' => $companyData['giro'],
                    'activity_code' => $companyData['activity_code'],
                    'commune' => $companyData['commune'],
                    'city' => $companyData['city'],
                    'logo_color' => $companyData['logo_color'],
                    'logo_black' => $companyData['logo_black'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                if (!empty($_SESSION['user']) && (int)($_SESSION['user']['company_id'] ?? 0) === $companyId) {
                    $_SESSION['user']['company_name'] = $companyData['name'];
                }
            }
        }

        if ($section === 'billing') {
            $this->settings->set('billing_defaults', [
                'notice_days_1' => (int)($_POST['notice_days_1'] ?? 15),
                'notice_days_2' => (int)($_POST['notice_days_2'] ?? 5),
                'send_time' => trim($_POST['send_time'] ?? '09:00'),
                'timezone' => trim($_POST['timezone'] ?? 'America/Santiago'),
                'invoice_prefix' => trim($_POST['invoice_prefix'] ?? 'FAC-'),
            ]);
            $this->settings->set('invoice_prefix', trim($_POST['invoice_prefix'] ?? 'FAC-'));
        }

        if ($section === 'invoice') {
            $this->settings->set('invoice_defaults', [
                'currency' => trim($_POST['currency'] ?? 'CLP'),
                'tax_rate' => (float)($_POST['tax_rate'] ?? 0),
                'apply_tax' => !empty($_POST['apply_tax']),
            ]);
            $this->settings->set('currency_format', [
                'symbol' => trim($_POST['currency_symbol'] ?? ($this->config['currency_format']['symbol'] ?? '$')),
                'decimals' => (int)($_POST['currency_decimals'] ?? ($this->config['currency_format']['decimals'] ?? 0)),
                'thousands_separator' => trim($_POST['currency_thousands_separator'] ?? ($this->config['currency_format']['thousands_separator'] ?? '.')),
                'decimal_separator' => $this->config['currency_format']['decimal_separator'] ?? ',',
            ]);
        }

        audit($this->db, Auth::user()['id'], 'update', 'settings');
        flash('success', 'Configuración actualizada correctamente.');
        $this->redirect('index.php?route=settings');
    }

    public function testSmtp(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
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
            $this->redirect('index.php?route=settings');
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

        $this->redirect('index.php?route=settings');
    }
}
