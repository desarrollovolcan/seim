<?php

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $companies = (new CompaniesModel($this->db))->active();
        $companyLogos = [];
        if (!empty($companies)) {
            $settingsModel = new SettingsModel($this->db);
            foreach ($companies as $company) {
                $companyId = (int)($company['id'] ?? 0);
                if ($companyId === 0) {
                    continue;
                }
                $settings = $settingsModel->get('company', [], $companyId);
                $companyLogos[$companyId] = login_logo_src($settings);
            }
        }
        $this->renderPublic('auth/login', [
            'title' => 'Acceso Administrador',
            'pageTitle' => 'Acceso Administrador',
            'hidePortalHeader' => true,
            'companies' => $companies,
            'hasCompanies' => !empty($companies),
            'companyLogos' => $companyLogos,
        ]);
    }

    public function login(): void
    {
        verify_csrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $companyId = (int)($_POST['company_id'] ?? 0);

        if ($companyId === 0) {
            $_SESSION['error'] = 'Selecciona una empresa.';
            $this->redirect('login.php');
        }

        $company = $this->db->fetch('SELECT * FROM companies WHERE id = :id', ['id' => $companyId]);
        if (!$company) {
            $_SESSION['error'] = 'Empresa no encontrada.';
            $this->redirect('login.php');
        }

        $user = $this->db->fetch(
            'SELECT users.*, roles.name as role FROM users JOIN roles ON users.role_id = roles.id WHERE users.email = :email AND users.deleted_at IS NULL',
            ['email' => $email]
        );
        if ($user) {
            $companyIds = user_company_ids($this->db, $user);
            if (!in_array($companyId, $companyIds, true)) {
                $user = null;
            }
        }

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Credenciales inválidas.';
            $this->redirect('login.php');
        }

        Auth::login([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'role_id' => $user['role_id'],
            'avatar_path' => $user['avatar_path'] ?? null,
            'company_id' => $company['id'],
            'company_name' => $company['name'],
        ]);
        $this->redirect('index.php');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('login.php');
    }

    public function switchCompany(): void
    {
        $this->requireLogin();
        if (!can_access_route($this->db, 'auth/switch-company', Auth::user())) {
            $this->redirect('index.php?route=dashboard');
        }
        $companyIds = user_company_ids($this->db, Auth::user());
        $companies = [];
        if ($companyIds) {
            $placeholders = implode(',', array_fill(0, count($companyIds), '?'));
            $companies = $this->db->fetchAll(
                'SELECT * FROM companies WHERE id IN (' . $placeholders . ') ORDER BY name',
                array_values($companyIds)
            );
        }
        $this->render('auth/switch-company', [
            'title' => 'Cambiar empresa',
            'pageTitle' => 'Cambiar empresa',
            'companies' => $companies,
            'currentCompanyId' => (int)(Auth::user()['company_id'] ?? 0),
        ]);
    }

    public function updateCompany(): void
    {
        $this->requireLogin();
        if (!can_access_route($this->db, 'auth/switch-company', Auth::user())) {
            $this->redirect('index.php?route=dashboard');
        }
        verify_csrf();
        $companyId = (int)($_POST['company_id'] ?? 0);
        $companyIds = user_company_ids($this->db, Auth::user());
        if (!in_array($companyId, $companyIds, true)) {
            flash('error', 'No tienes acceso a esa empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $company = $this->db->fetch('SELECT id, name FROM companies WHERE id = :id', ['id' => $companyId]);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $_SESSION['user']['company_id'] = $companyId;
        $_SESSION['user']['company_name'] = $company['name'];
        flash('success', 'Empresa cambiada correctamente.');
        $this->redirect('index.php?route=dashboard');
    }
}
