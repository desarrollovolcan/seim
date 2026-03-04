<?php

class PettyCashReceiptsModel extends Model
{
    protected string $table = 'petty_cash_receipts';

    public function listWithFilters(int $companyId, array $filters = []): array
    {
        $where = ['r.company_id = :company_id', 'r.deleted_at IS NULL'];
        $params = ['company_id' => $companyId];

        if (!empty($filters['date_from'])) {
            $where[] = 'r.receipt_date >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'r.receipt_date <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['supplier'])) {
            $where[] = 'r.supplier_name LIKE :supplier';
            $params['supplier'] = '%' . $filters['supplier'] . '%';
        }

        return $this->db->fetchAll(
            'SELECT r.*, u.name AS created_by_name,
                    (SELECT COUNT(*) FROM petty_cash_receipt_items i WHERE i.receipt_id = r.id) AS items_count
             FROM petty_cash_receipts r
             LEFT JOIN users u ON u.id = r.created_by
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY r.receipt_date DESC, r.id DESC',
            $params
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM petty_cash_receipts WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
