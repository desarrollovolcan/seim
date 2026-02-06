<?php

class PosSessionsModel extends Model
{
    protected string $table = 'pos_sessions';

    public function openSession(int $companyId, int $userId, float $openingAmount, string $saleContext = 'local'): int
    {
        if (!in_array($saleContext, ['local', 'camion'], true)) {
            $saleContext = 'local';
        }

        $data = [
            'company_id' => $companyId,
            'user_id' => $userId,
            'opening_amount' => $openingAmount,
            'status' => 'abierto',
            'opened_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($this->hasColumn('sale_context')) {
            $data['sale_context'] = $saleContext;
        }

        return $this->create($data);
    }

    public function activeForUser(int $companyId, int $userId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM pos_sessions WHERE company_id = :company_id AND user_id = :user_id AND status = "abierto" ORDER BY id DESC LIMIT 1',
            ['company_id' => $companyId, 'user_id' => $userId]
        );
    }

    public function closeSession(int $id, float $closingAmount): bool
    {
        return $this->update($id, [
            'closing_amount' => $closingAmount,
            'status' => 'cerrado',
            'closed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function listSummary(int $companyId, int $limit = 10): array
    {
        $limit = max(1, $limit);
        $sql = '
            SELECT ps.*,
                   u.name AS user_name,
                   COALESCE(sales.total, 0) AS sales_total,
                   COALESCE(withdrawals.total, 0) AS withdrawals_total
            FROM pos_sessions ps
            LEFT JOIN users u ON ps.user_id = u.id
            LEFT JOIN (
                SELECT s.pos_session_id, SUM(sp.amount) AS total
                FROM sales s
                INNER JOIN sale_payments sp ON sp.sale_id = s.id
                GROUP BY s.pos_session_id
            ) sales ON sales.pos_session_id = ps.id
            LEFT JOIN (
                SELECT pos_session_id, SUM(amount) AS total
                FROM pos_session_withdrawals
                GROUP BY pos_session_id
            ) withdrawals ON withdrawals.pos_session_id = ps.id
            WHERE ps.company_id = :company_id
            ORDER BY ps.opened_at DESC
            LIMIT ' . $limit;
        return $this->db->fetchAll($sql, ['company_id' => $companyId]);
    }
}
