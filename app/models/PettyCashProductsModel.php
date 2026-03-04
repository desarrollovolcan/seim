<?php

class PettyCashProductsModel extends Model
{
    protected string $table = 'petty_cash_products';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM petty_cash_products WHERE company_id = :company_id AND deleted_at IS NULL ORDER BY name ASC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM petty_cash_products WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
