<?php

class RolesController extends Controller
{
    private RolesModel $roles;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->roles = new RolesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $roles = $this->roles->all();
        $userCounts = $this->db->fetchAll('SELECT role_id, COUNT(*) as total FROM users WHERE deleted_at IS NULL GROUP BY role_id');
        $countByRole = [];
        foreach ($userCounts as $row) {
            $countByRole[(int)$row['role_id']] = (int)$row['total'];
        }

        $this->render('roles/index', [
            'title' => 'Roles de usuarios',
            'pageTitle' => 'Roles de usuarios',
            'roles' => $roles,
            'countByRole' => $countByRole,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->render('roles/create', [
            'title' => 'Nuevo rol',
            'pageTitle' => 'Nuevo rol',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'Ingresa el nombre del rol.');
            $this->redirect('index.php?route=roles/create');
        }
        $existing = $this->db->fetch('SELECT id FROM roles WHERE name = :name', ['name' => $name]);
        if ($existing) {
            flash('error', 'Ya existe un rol con ese nombre.');
            $this->redirect('index.php?route=roles/create');
        }

        $now = date('Y-m-d H:i:s');
        $roleId = $this->roles->create([
            'name' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'roles', $roleId);
        flash('success', 'Rol creado correctamente.');
        $this->redirect('index.php?route=roles');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $role = $this->roles->find($id);
        if (!$role) {
            flash('error', 'Rol no encontrado.');
            $this->redirect('index.php?route=roles');
        }
        $this->render('roles/edit', [
            'title' => 'Editar rol',
            'pageTitle' => 'Editar rol',
            'role' => $role,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $role = $this->roles->find($id);
        if (!$role) {
            flash('error', 'Rol no encontrado.');
            $this->redirect('index.php?route=roles');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'Ingresa el nombre del rol.');
            $this->redirect('index.php?route=roles/edit&id=' . $id);
        }
        $existing = $this->db->fetch('SELECT id FROM roles WHERE name = :name AND id != :id', ['name' => $name, 'id' => $id]);
        if ($existing) {
            flash('error', 'Ya existe un rol con ese nombre.');
            $this->redirect('index.php?route=roles/edit&id=' . $id);
        }
        $this->roles->update($id, [
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'roles', $id);
        flash('success', 'Rol actualizado correctamente.');
        $this->redirect('index.php?route=roles');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $role = $this->roles->find($id);
        if (!$role) {
            flash('error', 'Rol no encontrado.');
            $this->redirect('index.php?route=roles');
        }
        $usage = $this->db->fetch('SELECT COUNT(*) as total FROM users WHERE role_id = :id AND deleted_at IS NULL', ['id' => $id]);
        if (!empty($usage['total'])) {
            flash('error', 'No se puede eliminar el rol porque tiene usuarios asociados.');
            $this->redirect('index.php?route=roles');
        }
        $this->db->execute('DELETE FROM role_permissions WHERE role_id = :id', ['id' => $id]);
        $this->db->execute('DELETE FROM roles WHERE id = :id', ['id' => $id]);
        audit($this->db, Auth::user()['id'], 'delete', 'roles', $id);
        flash('success', 'Rol eliminado correctamente.');
        $this->redirect('index.php?route=roles');
    }
}
