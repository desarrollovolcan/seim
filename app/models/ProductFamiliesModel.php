<?php

class ProductFamiliesModel extends Model
{
    protected string $table = 'product_families';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM product_families WHERE company_id = :company_id ORDER BY name ASC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM product_families WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
