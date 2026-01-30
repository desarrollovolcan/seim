<?php

class ServiceRenewalsModel extends Model
{
    protected string $table = 'service_renewals';

    public function active(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT * FROM service_renewals WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY id DESC',
            ['company_id' => $companyId]
        );
    }
}
