<?php

class OnlinePaymentsConfigController extends Controller
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
        $flowConfig = $this->settings->get('flow_payment_config', []);
        if (!is_array($flowConfig)) {
            $flowConfig = [];
        }
        $defaultBaseUrls = [
            'sandbox' => 'https://sandbox.flow.cl/api',
            'production' => 'https://www.flow.cl/api',
        ];
        $defaults = [
            'environment' => 'sandbox',
            'api_key' => '',
            'secret_key' => '',
            'base_url' => $defaultBaseUrls['sandbox'],
            'return_url' => '',
            'confirmation_url' => '',
        ];
        $flowConfig = array_merge($defaults, $flowConfig);
        if (empty($flowConfig['base_url'])) {
            $flowConfig['base_url'] = $defaultBaseUrls[$flowConfig['environment']] ?? $defaultBaseUrls['sandbox'];
        }
        $this->render('maintainers/online-payments-config', [
            'title' => 'Configuración de pagos en línea',
            'pageTitle' => 'Configuración de pagos en línea',
            'flowConfig' => $flowConfig,
            'defaultBaseUrls' => $defaultBaseUrls,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $defaultBaseUrls = [
            'sandbox' => 'https://sandbox.flow.cl/api',
            'production' => 'https://www.flow.cl/api',
        ];
        $environment = trim($_POST['environment'] ?? 'sandbox');
        if (!array_key_exists($environment, $defaultBaseUrls)) {
            $environment = 'sandbox';
        }
        $baseUrl = trim($_POST['base_url'] ?? '');
        if ($baseUrl === '') {
            $baseUrl = $defaultBaseUrls[$environment];
        }
        $this->settings->set('flow_payment_config', [
            'environment' => $environment,
            'api_key' => trim($_POST['api_key'] ?? ''),
            'secret_key' => trim($_POST['secret_key'] ?? ''),
            'base_url' => $baseUrl,
            'return_url' => trim($_POST['return_url'] ?? ''),
            'confirmation_url' => trim($_POST['confirmation_url'] ?? ''),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'flow_payment_config');
        flash('success', 'Configuración de pagos en línea actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/online-payments');
    }
}
