<?php

class RolePermissionsModel extends Model
{
    protected string $table = 'role_permissions';

    public function byRole(int $roleId): array
    {
        return $this->db->fetchAll('SELECT permission_key FROM role_permissions WHERE role_id = :role_id', [
            'role_id' => $roleId,
        ]);
    }

    public function replaceForRole(int $roleId, array $keys): void
    {
        $this->db->execute('DELETE FROM role_permissions WHERE role_id = :role_id', ['role_id' => $roleId]);
        foreach ($keys as $key) {
            $this->create([
                'role_id' => $roleId,
                'permission_key' => $key,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
