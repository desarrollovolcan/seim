<?php

class ProductSubfamiliesModel extends Model
{
    protected string $table = 'product_subfamilies';

    public function active(int $companyId, ?int $familyId = null): array
    {
        $params = ['company_id' => $companyId];
        $where = 'company_id = :company_id';
        if ($familyId !== null) {
            $where .= ' AND family_id = :family_id';
            $params['family_id'] = $familyId;
        }
        return $this->db->fetchAll(
            "SELECT * FROM product_subfamilies WHERE {$where} ORDER BY name ASC",
            $params
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM product_subfamilies WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
