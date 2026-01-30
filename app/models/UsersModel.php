<?php

class UsersModel extends Model
{
    protected string $table = 'users';

    public function allActive(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT users.*, roles.name as role FROM users JOIN roles ON users.role_id = roles.id WHERE users.deleted_at IS NULL AND users.company_id = :company_id ORDER BY users.id DESC',
            ['company_id' => $companyId]
        );
    }
}
