<?php

class SalesDispatchesModel extends Model
{
    protected string $table = 'sales_dispatches';

    private function hasPosSessionsTable(): bool
    {
        return table_exists($this->db, 'pos_sessions');
    }

    public function listWithRelations(int $companyId): array
    {
        $joinPos = $this->hasPosSessionsTable()
            ? 'LEFT JOIN pos_sessions ps ON ps.id = sd.pos_session_id'
            : '';
        $sessionCode = $this->hasPosSessionsTable() ? 'ps.session_code' : 'NULL AS session_code';

        $rows = $this->db->fetchAll(
            "SELECT sd.*, {$sessionCode},
                    COALESCE((SELECT SUM(quantity_dispatched) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_dispatched,
                    COALESCE((SELECT SUM(empty_returned_total) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_empty_returned,
                    COALESCE((SELECT SUM(empty_merma) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_merma,
                    COALESCE((SELECT SUM(s.total)
                              FROM sales s
                              WHERE s.pos_session_id = sd.pos_session_id), 0) AS pos_sales_total
             FROM sales_dispatches sd
             {$joinPos}
             WHERE sd.company_id = :company_id
             ORDER BY sd.dispatch_date DESC, sd.id DESC",
            ['company_id' => $companyId]
        );

        if (!empty($rows)) {
            return $rows;
        }

        $count = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM sales_dispatches WHERE company_id = :company_id',
            ['company_id' => $companyId]
        );
        if ((int)($count['total'] ?? 0) === 0) {
            return [];
        }

        return $this->db->fetchAll(
            'SELECT sd.*, NULL AS session_code,
                    COALESCE((SELECT SUM(quantity_dispatched) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_dispatched,
                    COALESCE((SELECT SUM(empty_returned_total) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_empty_returned,
                    COALESCE((SELECT SUM(empty_merma) FROM sales_dispatch_items WHERE dispatch_id = sd.id), 0) AS total_merma,
                    0 AS pos_sales_total
             FROM sales_dispatches sd
             WHERE sd.company_id = :company_id
             ORDER BY sd.dispatch_date DESC, sd.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function findWithRelations(int $id, int $companyId): ?array
    {
        $joinPos = $this->hasPosSessionsTable()
            ? 'LEFT JOIN pos_sessions ps ON ps.id = sd.pos_session_id'
            : '';
        $sessionCode = $this->hasPosSessionsTable() ? 'ps.session_code' : 'NULL AS session_code';

        $row = $this->db->fetch(
            "SELECT sd.*, {$sessionCode},
                    COALESCE((SELECT SUM(s.total)
                              FROM sales s
                              WHERE s.pos_session_id = sd.pos_session_id), 0) AS pos_sales_total
             FROM sales_dispatches sd
             {$joinPos}
             WHERE sd.id = :id AND sd.company_id = :company_id",
            ['id' => $id, 'company_id' => $companyId]
        );

        if ($row) {
            return $row;
        }

        return $this->db->fetch(
            'SELECT sd.*, NULL AS session_code, 0 AS pos_sales_total
             FROM sales_dispatches sd
             WHERE sd.id = :id AND sd.company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
