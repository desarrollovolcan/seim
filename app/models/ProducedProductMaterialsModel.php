<?php

class ProducedProductMaterialsModel extends Model
{
    protected string $table = 'produced_product_materials';

    public function byProducedProduct(int $producedProductId): array
    {
        return $this->db->fetchAll(
            'SELECT ppm.*, p.name AS product_name, p.cost AS product_cost
             FROM produced_product_materials ppm
             JOIN products p ON p.id = ppm.product_id
             WHERE ppm.produced_product_id = :produced_product_id
             ORDER BY ppm.id ASC',
            ['produced_product_id' => $producedProductId]
        );
    }

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT ppm.*, p.name AS product_name, p.cost AS product_cost
             FROM produced_product_materials ppm
             JOIN produced_products pp ON pp.id = ppm.produced_product_id
             JOIN products p ON p.id = ppm.product_id
             WHERE pp.company_id = :company_id AND p.company_id = :company_id
             ORDER BY ppm.produced_product_id ASC, ppm.id ASC',
            ['company_id' => $companyId]
        );
    }

    public function replaceForProduct(int $producedProductId, array $materials): void
    {
        $this->db->execute(
            'DELETE FROM produced_product_materials WHERE produced_product_id = :produced_product_id',
            ['produced_product_id' => $producedProductId]
        );
        if (!$materials) {
            return;
        }
        foreach ($materials as $material) {
            $this->create([
                'produced_product_id' => $producedProductId,
                'product_id' => $material['product_id'],
                'quantity' => $material['quantity'],
                'unit_cost' => $material['unit_cost'],
                'subtotal' => $material['subtotal'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
