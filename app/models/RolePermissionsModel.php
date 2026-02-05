<?php

class RolePermissionsModel extends Model
{
    protected string $table = 'role_permissions';

    public function byRole(int $roleId): array
    {
        if (!$this->tableExists()) {
            return [];
        }

        return $this->db->fetchAll('SELECT permission_key FROM role_permissions WHERE role_id = :role_id', [
            'role_id' => $roleId,
        ]);
    }

    public function replaceForRole(int $roleId, array $keys): void
    {
        if (!$this->tableExists()) {
            $this->createTable();
        }

        $this->db->execute('DELETE FROM role_permissions WHERE role_id = :role_id', ['role_id' => $roleId]);
        foreach ($keys as $key) {
            $this->create([
                'role_id' => $roleId,
                'permission_key' => $key,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function tableExists(): bool
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table',
            ['table' => $this->table]
        );
        return (int)($row['total'] ?? 0) > 0;
    }

    private function createTable(): void
    {
        $this->db->execute(
            'CREATE TABLE IF NOT EXISTS role_permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                role_id INT NOT NULL,
                permission_key VARCHAR(120) NOT NULL,
                created_at DATETIME NOT NULL,
                UNIQUE KEY idx_role_permission_unique (role_id, permission_key),
                INDEX idx_role_permissions_role (role_id),
                CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci'
        );
    }
}
