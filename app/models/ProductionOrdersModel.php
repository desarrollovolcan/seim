<?php

class ProductionOrdersModel extends Model
{
    protected string $table = 'production_orders';

    public function listWithTotals(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT po.*, COALESCE(SUM(o.quantity), 0) AS total_quantity
             FROM production_orders po
             LEFT JOIN production_outputs o ON po.id = o.production_id
             WHERE po.company_id = :company_id
             GROUP BY po.id
             ORDER BY po.production_date DESC, po.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM production_orders WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
