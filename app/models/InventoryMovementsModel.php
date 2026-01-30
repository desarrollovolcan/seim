<?php

class InventoryMovementsModel extends Model
{
    protected string $table = 'inventory_movements';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT im.*, p.name as product_name
             FROM inventory_movements im
             JOIN products p ON im.product_id = p.id
             WHERE im.company_id = :company_id
             ORDER BY im.movement_date DESC, im.id DESC',
            ['company_id' => $companyId]
        );
    }
}
