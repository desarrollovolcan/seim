<?php

class HrContractTypesModel extends Model
{
    protected string $table = 'hr_contract_types';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE company_id = :company_id AND deleted_at IS NULL ORDER BY name ASC",
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL",
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
