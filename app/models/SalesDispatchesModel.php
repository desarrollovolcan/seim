<?php

class SalesDispatchesModel extends Model
{
    protected string $table = 'sales_dispatches';

    public function listWithRelations(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT sd.*, ps.session_code,
                    COALESCE((SELECT SUM(quantity_dispatched) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_dispatched,
                    COALESCE((SELECT SUM(empty_returned_total) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_empty_returned,
                    COALESCE((SELECT SUM(empty_merma) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_merma,
                    COALESCE((SELECT SUM(si.quantity * si.unit_price)
                              FROM sales s
                              JOIN sale_items si ON si.sale_id = s.id
                              WHERE s.pos_session_id = sd.pos_session_id), 0) AS pos_sales_total
             FROM sales_dispatches sd
             LEFT JOIN pos_sessions ps ON ps.id = sd.pos_session_id
             WHERE sd.company_id = :company_id
             ORDER BY sd.dispatch_date DESC, sd.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function findWithRelations(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT sd.*, ps.session_code,
                    COALESCE((SELECT SUM(si.quantity * si.unit_price)
                              FROM sales s
                              JOIN sale_items si ON si.sale_id = s.id
                              WHERE s.pos_session_id = sd.pos_session_id), 0) AS pos_sales_total
             FROM sales_dispatches sd
             LEFT JOIN pos_sessions ps ON ps.id = sd.pos_session_id
             WHERE sd.id = :id AND sd.company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
