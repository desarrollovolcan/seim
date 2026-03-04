<?php

class InvoiceRegisterModel extends Model
{
    protected string $table = 'purchase_invoice_records';

    public function listWithFilters(int $companyId, array $filters = []): array
    {
        $where = ['r.company_id = :company_id', 'r.deleted_at IS NULL'];
        $params = ['company_id' => $companyId];

        if (!empty($filters['date_from'])) {
            $where[] = 'r.invoice_date >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'r.invoice_date <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['supplier'])) {
            $where[] = 'r.supplier_name LIKE :supplier';
            $params['supplier'] = '%' . $filters['supplier'] . '%';
        }
        if (!empty($filters['invoice_number'])) {
            $where[] = 'r.invoice_number LIKE :invoice_number';
            $params['invoice_number'] = '%' . $filters['invoice_number'] . '%';
        }

        return $this->db->fetchAll(
            'SELECT r.*, u.name AS created_by_name,
                    (SELECT COUNT(*) FROM purchase_invoice_record_items i WHERE i.invoice_id = r.id) AS items_count
             FROM purchase_invoice_records r
             LEFT JOIN users u ON u.id = r.created_by
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY r.invoice_date DESC, r.id DESC',
            $params
        );
    }
}
