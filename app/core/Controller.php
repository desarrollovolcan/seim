<?php

class Controller
{
    protected array $config;
    protected Database $db;

    public function __construct(array $config, Database $db)
    {
        $this->config = $config;
        $this->db = $db;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $config = $this->config;
        $db = $this->db;
        $currentUser = Auth::user();
        $permissions = [];
        if ($currentUser && ($currentUser['role'] ?? '') !== 'admin') {
            $roleId = (int)($currentUser['role_id'] ?? 0);
            if ($roleId === 0 && !empty($currentUser['role'])) {
                $roleRow = $this->db->fetch('SELECT id FROM roles WHERE name = :name', ['name' => $currentUser['role']]);
                $roleId = (int)($roleRow['id'] ?? 0);
            }
            if ($roleId) {
                $permissions = role_permissions($this->db, $roleId);
            }
        }
        try {
            $companyId = current_company_id();
            $notifications = $companyId
                ? $this->db->fetchAll(
                    "SELECT * FROM notifications WHERE read_at IS NULL AND company_id = :company_id ORDER BY created_at DESC LIMIT 5",
                    ['company_id' => $companyId]
                )
                : [];
        } catch (PDOException $e) {
            log_message('error', 'Failed to load notifications: ' . $e->getMessage());
            $notifications = [];
        }
        $notificationCount = count($notifications);
        $currentCompany = null;
        $companyId = current_company_id();
        try {
            $companySettings = login_company_settings($this->db);
        } catch (Throwable $e) {
            log_message('error', 'Failed to load company settings: ' . $e->getMessage());
            $companySettings = [];
        }
        if ($companyId) {
            try {
                $currentCompany = $this->db->fetch('SELECT * FROM companies WHERE id = :id', ['id' => $companyId]);
            } catch (Throwable $e) {
                log_message('error', 'Failed to load company: ' . $e->getMessage());
                $currentCompany = null;
            }
        }
        include __DIR__ . '/../views/layouts/main.php';
    }

    protected function renderPublic(string $view, array $data = []): void
    {
        extract($data);
        $config = $this->config;
        try {
            $settingsModel = new SettingsModel($this->db);
            $companySettings = $settingsModel->get('company', []);
        } catch (Throwable $e) {
            log_message('error', 'Failed to load company settings: ' . $e->getMessage());
            $companySettings = [];
        }
        $currentCompany = null;
        $companyId = current_company_id();
        if ($companyId) {
            try {
                $currentCompany = $this->db->fetch('SELECT * FROM companies WHERE id = :id', ['id' => $companyId]);
            } catch (Throwable $e) {
                log_message('error', 'Failed to load company: ' . $e->getMessage());
                $currentCompany = null;
            }
        }
        include __DIR__ . '/../views/layouts/portal.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.php');
        }
    }

    protected function requireRole(string $role): void
    {
        $user = Auth::user();
        if (!$user || $user['role'] !== $role) {
            $this->redirect('index.php?route=dashboard');
        }
    }
}
