<?php

class SalesOrdersModel extends Model
{
    protected string $table = 'sales_orders';

    public function active(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT * FROM sales_orders WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY id DESC',
            ['company_id' => $companyId]
        );
    }
}
