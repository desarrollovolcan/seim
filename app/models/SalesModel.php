<?php

class SalesModel extends Model
{
    protected string $table = 'sales';

    public function recentBySession(int $sessionId, int $companyId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT s.id, s.numero, s.sale_date, s.total, s.status, c.name AS client_name
             FROM sales s
             LEFT JOIN clients c ON s.client_id = c.id
             WHERE s.company_id = :company_id AND s.pos_session_id = :session_id AND s.deleted_at IS NULL
             ORDER BY s.sale_date DESC, s.id DESC
             LIMIT ' . (int)$limit,
            ['company_id' => $companyId, 'session_id' => $sessionId]
        );
    }

    public function listWithRelations(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT s.*, c.name AS client_name
             FROM sales s
             LEFT JOIN clients c ON s.client_id = c.id
             WHERE s.company_id = :company_id AND s.deleted_at IS NULL
             ORDER BY s.sale_date DESC, s.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT s.*, c.name AS client_name
             FROM sales s
             LEFT JOIN clients c ON s.client_id = c.id
             WHERE s.id = :id AND s.company_id = :company_id AND s.deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function nextNumber(string $prefix, int $companyId): string
    {
        $row = $this->db->fetch(
            'SELECT numero FROM sales WHERE company_id = :company_id AND numero LIKE :prefix ORDER BY id DESC LIMIT 1',
            ['company_id' => $companyId, 'prefix' => $prefix . '%']
        );
        $last = 0;
        if ($row && preg_match('/' . preg_quote($prefix, '/') . '(\\d+)/', (string)($row['numero'] ?? ''), $matches)) {
            $last = (int)($matches[1] ?? 0);
        }
        return $prefix . str_pad((string)($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
