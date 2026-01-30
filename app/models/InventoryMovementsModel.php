<?php

class InventoryMovementsModel extends Model
{
    protected string $table = 'inventory_movements';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT im.*, COALESCE(p.name, pp.name) as product_name
             FROM inventory_movements im
             LEFT JOIN products p ON im.product_id = p.id
             LEFT JOIN produced_products pp ON im.produced_product_id = pp.id
             WHERE im.company_id = :company_id
             ORDER BY im.movement_date DESC, im.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function supportsProducedProductId(): bool
    {
        return $this->hasColumn('produced_product_id');
    }

    public function supportsProductId(): bool
    {
        return $this->hasColumn('product_id');
    }
}
