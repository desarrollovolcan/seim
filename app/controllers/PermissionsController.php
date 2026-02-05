<?php

class PermissionsController extends Controller
{
    private RolesModel $roles;
    private RolePermissionsModel $permissions;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->roles = new RolesModel($db);
        $this->permissions = new RolePermissionsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $roles = $this->roles->all();
        $selectedRoleId = (int)($_GET['role_id'] ?? ($roles[0]['id'] ?? 0));
        $selectedPermissions = [];
        if ($selectedRoleId) {
            $rows = $this->permissions->byRole($selectedRoleId);
            $selectedPermissions = array_map(static fn(array $row) => $row['permission_key'], $rows);
        }

        $this->render('users/permissions', [
            'title' => 'Permisos de usuarios',
            'pageTitle' => 'Permisos de usuarios',
            'roles' => $roles,
            'selectedRoleId' => $selectedRoleId,
            'selectedPermissions' => $selectedPermissions,
            'permissionCatalog' => permission_catalog(),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $roleId = (int)($_POST['role_id'] ?? 0);
        $permissions = $_POST['permissions'] ?? [];
        $permissions = array_values(array_filter($permissions, 'is_string'));
        if ($roleId) {
            try {
                $this->permissions->replaceForRole($roleId, $permissions);
                audit($this->db, Auth::user()['id'], 'update', 'role_permissions', $roleId);
                flash('success', 'Permisos actualizados correctamente.');
            } catch (Throwable $e) {
                error_log('Error al actualizar permisos: ' . $e->getMessage());
                flash('error', 'No se pudieron guardar los permisos. Revisa la configuración de base de datos e intenta nuevamente.');
            }
        }
        $this->redirect('index.php?route=users/permissions&role_id=' . $roleId);
    }
}
