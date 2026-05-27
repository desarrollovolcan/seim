<?php

class ProductsModel extends Model
{
    protected string $table = 'products';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT *
             FROM products
             WHERE company_id = :company_id
             ORDER BY name ASC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM products WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function adjustStock(int $id, int $difference): void
    {
        $this->db->execute(
            'UPDATE products SET stock = GREATEST(0, stock + :difference), updated_at = NOW() WHERE id = :id',
            [
                'difference' => $difference,
                'id' => $id,
            ]
        );
    }

    public function updateCost(int $id, float $cost): void
    {
        $this->db->execute(
            'UPDATE products SET cost = :cost, updated_at = NOW() WHERE id = :id',
            [
                'cost' => $cost,
                'id' => $id,
            ]
        );
    }

    public function latestCompetitionCode(int $companyId, string $prefix, ?int $excludeId = null): ?string
    {
        $params = [
            'company_id' => $companyId,
            'prefix' => $prefix . '%',
        ];
        $where = 'company_id = :company_id AND competition_code LIKE :prefix';
        if ($excludeId) {
            $where .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $row = $this->db->fetch(
            "SELECT competition_code FROM products WHERE {$where} ORDER BY competition_code DESC LIMIT 1",
            $params
        );

        return $row['competition_code'] ?? null;
    }

    public function latestSupplierCode(int $companyId, string $prefix, ?int $excludeId = null): ?string
    {
        $params = [
            'company_id' => $companyId,
            'prefix' => $prefix . '%',
        ];
        $where = 'company_id = :company_id AND supplier_code LIKE :prefix';
        if ($excludeId) {
            $where .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $row = $this->db->fetch(
            "SELECT supplier_code FROM products WHERE {$where} ORDER BY supplier_code DESC LIMIT 1",
            $params
        );

        return $row['supplier_code'] ?? null;
    }
}
