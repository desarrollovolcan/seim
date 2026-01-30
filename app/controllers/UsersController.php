<?php

class UsersController extends Controller
{
    private UsersModel $users;
    private RolesModel $roles;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->users = new UsersModel($db);
        $this->roles = new RolesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $users = $this->users->allActive(current_company_id());
        $this->render('users/index', [
            'title' => 'Usuarios',
            'pageTitle' => 'Usuarios',
            'users' => $users,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $roles = $this->roles->all();
        $companies = (new CompaniesModel($this->db))->active();
        $this->render('users/create', [
            'title' => 'Nuevo Usuario',
            'pageTitle' => 'Nuevo Usuario',
            'roles' => $roles,
            'companies' => $companies,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $companyIds = array_values(array_filter(array_map('intval', $_POST['company_ids'] ?? [])));
        $companyId = (int)($_POST['company_id'] ?? 0);
        if ($companyId > 0) {
            $companyIds[] = $companyId;
        }
        $companyIds = array_values(array_unique($companyIds));
        if (empty($companyIds)) {
            flash('error', 'Selecciona una empresa válida.');
            $this->redirect('index.php?route=users/create');
        }
        $validCompanyCount = $this->db->fetch(
            'SELECT COUNT(*) as total FROM companies WHERE id IN (' . implode(',', array_fill(0, count($companyIds), '?')) . ')',
            $companyIds
        );
        if ((int)($validCompanyCount['total'] ?? 0) !== count($companyIds)) {
            flash('error', 'Selecciona empresas válidas.');
            $this->redirect('index.php?route=users/create');
        }
        if (!Validator::required($name) || !Validator::email($email)) {
            flash('error', 'Completa los campos obligatorios.');
            $this->redirect('index.php?route=users/create');
        }
        $avatarResult = upload_avatar($_FILES['avatar'] ?? null, 'user');
        if (!empty($avatarResult['error'])) {
            flash('error', $avatarResult['error']);
            $this->redirect('index.php?route=users/create');
        }

        $primaryCompanyId = $companyIds[0];
        $userId = $this->users->create([
            'company_id' => $primaryCompanyId,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'role_id' => (int)($_POST['role_id'] ?? 2),
            'avatar_path' => $avatarResult['path'],
            'signature' => trim($_POST['signature'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->syncUserCompanies($userId, $companyIds);
        foreach ($companyIds as $assignedCompanyId) {
            create_notification(
                $this->db,
                (int)$assignedCompanyId,
                'Nuevo usuario',
                'Se creó el usuario "' . $name . '".',
                'success'
            );
        }
        audit($this->db, Auth::user()['id'], 'create', 'users');
        flash('success', 'Usuario creado correctamente.');
        $this->redirect('index.php?route=users');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->db->fetch(
            'SELECT * FROM users WHERE id = :id AND deleted_at IS NULL',
            ['id' => $id]
        );
        $roles = $this->roles->all();
        $companies = (new CompaniesModel($this->db))->active();
        $this->render('users/edit', [
            'title' => 'Editar Usuario',
            'pageTitle' => 'Editar Usuario',
            'user' => $user,
            'roles' => $roles,
            'companies' => $companies,
            'userCompanyIds' => user_company_ids($this->db, $user),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $companyIds = array_values(array_filter(array_map('intval', $_POST['company_ids'] ?? [])));
        $companyId = (int)($_POST['company_id'] ?? 0);
        if ($companyId > 0) {
            $companyIds[] = $companyId;
        }
        $companyIds = array_values(array_unique($companyIds));
        if (empty($companyIds)) {
            flash('error', 'Selecciona una empresa válida.');
            $this->redirect('index.php?route=users/edit&id=' . $id);
        }
        $validCompanyCount = $this->db->fetch(
            'SELECT COUNT(*) as total FROM companies WHERE id IN (' . implode(',', array_fill(0, count($companyIds), '?')) . ')',
            $companyIds
        );
        if ((int)($validCompanyCount['total'] ?? 0) !== count($companyIds)) {
            flash('error', 'Selecciona empresas válidas.');
            $this->redirect('index.php?route=users/edit&id=' . $id);
        }
        if (!Validator::required($name) || !Validator::email($email)) {
            flash('error', 'Completa los campos obligatorios.');
            $this->redirect('index.php?route=users/edit&id=' . $id);
        }
        $primaryCompanyId = $companyIds[0];
        $data = [
            'company_id' => $primaryCompanyId,
            'name' => $name,
            'email' => $email,
            'role_id' => (int)($_POST['role_id'] ?? 2),
            'signature' => trim($_POST['signature'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $avatarResult = upload_avatar($_FILES['avatar'] ?? null, 'user');
        if (!empty($avatarResult['error'])) {
            flash('error', $avatarResult['error']);
            $this->redirect('index.php?route=users/edit&id=' . $id);
        }
        if (!empty($avatarResult['path'])) {
            $data['avatar_path'] = $avatarResult['path'];
        }
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        $this->users->update($id, $data);
        $this->syncUserCompanies($id, $companyIds);
        if (!empty($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === $id) {
            $_SESSION['user']['company_id'] = $primaryCompanyId;
            $companyRow = $this->db->fetch('SELECT name FROM companies WHERE id = :id', ['id' => $primaryCompanyId]);
            $_SESSION['user']['company_name'] = $companyRow['name'] ?? $_SESSION['user']['company_name'];
        }
        audit($this->db, Auth::user()['id'], 'update', 'users', $id);
        flash('success', 'Usuario actualizado correctamente.');
        $this->redirect('index.php?route=users');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $user = $this->db->fetch('SELECT id FROM users WHERE id = :id AND deleted_at IS NULL', ['id' => $id]);
        if (!$user) {
            flash('error', 'No encontramos el usuario.');
            $this->redirect('index.php?route=users');
        }
        $ticketAssigned = $this->db->fetch('SELECT COUNT(*) as total FROM support_tickets WHERE assigned_user_id = :id', ['id' => $id]);
        $ticketCreated = $this->db->fetch('SELECT COUNT(*) as total FROM support_tickets WHERE created_by_type = "user" AND created_by_id = :id', ['id' => $id]);
        if (!empty($ticketAssigned['total']) || !empty($ticketCreated['total'])) {
            flash('error', 'No se puede eliminar el usuario porque tiene tickets asociados.');
            $this->redirect('index.php?route=users');
        }
        $this->users->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'users', $id);
        flash('success', 'Usuario eliminado correctamente.');
        $this->redirect('index.php?route=users');
    }

    public function assignCompany(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companies = (new CompaniesModel($this->db))->active();
        $users = $this->db->fetchAll(
            'SELECT users.*, roles.name as role, companies.name as company_name
             FROM users
             JOIN roles ON users.role_id = roles.id
             LEFT JOIN companies ON users.company_id = companies.id
             WHERE users.deleted_at IS NULL
             ORDER BY users.name'
        );
        foreach ($users as &$user) {
            $user['company_ids'] = user_company_ids($this->db, $user);
        }
        unset($user);
        $this->render('users/assign_company', [
            'title' => 'Asociar usuario a empresa',
            'pageTitle' => 'Asociar usuario a empresa',
            'users' => $users,
            'companies' => $companies,
        ]);
    }

    public function updateCompany(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $companyIds = array_values(array_filter(array_map('intval', $_POST['company_ids'] ?? [])));
        $user = $this->db->fetch('SELECT id FROM users WHERE id = :id AND deleted_at IS NULL', ['id' => $userId]);
        if (!$user) {
            flash('error', 'Usuario no encontrado.');
            $this->redirect('index.php?route=users/assign-company');
        }
        if (empty($companyIds)) {
            flash('error', 'Selecciona al menos una empresa.');
            $this->redirect('index.php?route=users/assign-company');
        }
        $validCompanyCount = $this->db->fetch(
            'SELECT COUNT(*) as total FROM companies WHERE id IN (' . implode(',', array_fill(0, count($companyIds), '?')) . ')',
            $companyIds
        );
        if ((int)($validCompanyCount['total'] ?? 0) !== count($companyIds)) {
            flash('error', 'Empresas no encontradas.');
            $this->redirect('index.php?route=users/assign-company');
        }
        $primaryCompanyId = $companyIds[0];
        $primaryCompany = $this->db->fetch('SELECT id, name FROM companies WHERE id = :id', ['id' => $primaryCompanyId]);
        if (!$primaryCompany) {
            flash('error', 'Empresa no encontrada.');
            $this->redirect('index.php?route=users/assign-company');
        }
        $this->users->update($userId, [
            'company_id' => $primaryCompanyId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->syncUserCompanies($userId, $companyIds);
        if (!empty($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === $userId) {
            $_SESSION['user']['company_id'] = $primaryCompanyId;
            $_SESSION['user']['company_name'] = $primaryCompany['name'];
        }
        audit($this->db, Auth::user()['id'], 'update', 'users_company', $userId);
        flash('success', 'Empresa asociada correctamente.');
        $this->redirect('index.php?route=users/assign-company');
    }

    private function syncUserCompanies(int $userId, array $companyIds): void
    {
        $companyIds = array_values(array_unique(array_filter($companyIds)));
        $this->db->execute('DELETE FROM user_companies WHERE user_id = :user_id', ['user_id' => $userId]);
        foreach ($companyIds as $companyId) {
            $this->db->execute(
                'INSERT INTO user_companies (user_id, company_id, created_at) VALUES (:user_id, :company_id, NOW())',
                ['user_id' => $userId, 'company_id' => $companyId]
            );
        }
    }
}
