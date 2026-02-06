<?php

class SalesDispatchItemsModel extends Model
{
    protected string $table = 'sales_dispatch_items';

    public function byDispatch(int $dispatchId): array
    {
        return $this->db->fetchAll(
            'SELECT sdi.*, pp.name AS produced_product_name, pp.sku AS produced_product_sku
             FROM sales_dispatch_items sdi
             JOIN produced_products pp ON pp.id = sdi.produced_product_id
             WHERE sdi.dispatch_id = :dispatch_id
             ORDER BY sdi.id ASC',
            ['dispatch_id' => $dispatchId]
        );
    }
}
