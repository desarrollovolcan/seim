<?php

class ProducedProductsModel extends Model
{
    protected string $table = 'produced_products';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT *
             FROM produced_products
             WHERE company_id = :company_id AND deleted_at IS NULL
             ORDER BY name ASC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT *
             FROM produced_products
             WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function adjustStock(int $id, int $difference): void
    {
        $this->db->execute(
            'UPDATE produced_products
             SET stock = GREATEST(0, stock + :difference), updated_at = NOW()
             WHERE id = :id',
            [
                'difference' => $difference,
                'id' => $id,
            ]
        );
    }

    public function updateCost(int $id, float $cost): void
    {
        $this->db->execute(
            'UPDATE produced_products
             SET cost = :cost, updated_at = NOW()
             WHERE id = :id',
            [
                'cost' => $cost,
                'id' => $id,
            ]
        );
    }
}
