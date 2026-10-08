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
        if ($currentUser && !$this->isAdmin($currentUser)) {
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

    public function isAdmin(?array $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return false;
        }
        $roleId = (int)($user['role_id'] ?? 0);
        if ($roleId === 1) {
            return true;
        }
        $role = strtolower(rtrim(trim((string)($user['role'] ?? '')), '.'));
        return in_array($role, ['admin', 'administrador', 'superadmin'], true);
    }

    protected function requireRole(string $role): void
    {
        $this->requireLogin();
        $user = Auth::user();
        if (!$user) {
            $this->redirect('login.php');
        }
        $normalizedExpected = strtolower(rtrim(trim($role), '.'));
        if ($normalizedExpected === 'admin') {
            if ($this->isAdmin($user)) {
                return;
            }
        } else {
            $userRole = strtolower(rtrim(trim((string)($user['role'] ?? '')), '.'));
            if ($userRole === $normalizedExpected) {
                return;
            }
        }
        flash('error', 'No tienes permisos para acceder a esta sección.');
        $this->redirect('index.php?route=dashboard');
    }

    protected function requirePermission(string $permissionOrRoute): void
    {
        $this->requireLogin();
        $user = Auth::user();
        if (!$user) {
            $this->redirect('login.php');
        }
        if ($this->isAdmin($user)) {
            return;
        }
        if (can_user_access_route($this->db, $permissionOrRoute, $user)) {
            return;
        }
        $roleId = (int)($user['role_id'] ?? 0);
        if ($roleId) {
            $perms = role_permissions($this->db, $roleId);
            if (in_array($permissionOrRoute, $perms, true)) {
                return;
            }
            $editKey = permission_edit_key_for_view($permissionOrRoute);
            if ($editKey && in_array($editKey, $perms, true)) {
                return;
            }
            $legacyKey = permission_legacy_key_for($permissionOrRoute);
            if ($legacyKey && in_array($legacyKey, $perms, true)) {
                return;
            }
        }
        flash('error', 'No tienes permisos para acceder a esta sección.');
        $this->redirect('index.php?route=dashboard');
    }
}
