<?php

class SalePaymentsModel extends Model
{
    protected string $table = 'sale_payments';

    public function totalsBySession(int $sessionId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT method, SUM(amount) as total FROM sale_payments sp
             INNER JOIN sales s ON sp.sale_id = s.id
             WHERE s.pos_session_id = :session_id
             GROUP BY method',
            ['session_id' => $sessionId]
        );
        $totals = [];
        foreach ($rows as $row) {
            $totals[$row['method']] = (float)($row['total'] ?? 0);
        }
        return $totals;
    }
}
