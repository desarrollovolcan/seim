<?php

class ServicesModel extends Model
{
    protected string $table = 'services';

    public function active(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT services.*, clients.name as client_name
             FROM services
             LEFT JOIN clients ON services.client_id = clients.id
             WHERE services.deleted_at IS NULL
               AND services.company_id = :company_id
             ORDER BY services.id DESC',
            ['company_id' => $companyId]
        );
    }
}
