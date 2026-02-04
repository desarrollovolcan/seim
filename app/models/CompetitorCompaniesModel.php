<?php

class CompetitorCompaniesModel extends Model
{
    protected string $table = 'competitor_companies';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM competitor_companies WHERE company_id = :company_id ORDER BY name ASC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM competitor_companies WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
