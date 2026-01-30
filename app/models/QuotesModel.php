<?php

class QuotesModel extends Model
{
    protected string $table = 'quotes';

    public function allWithClient(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        if (!$companyId) {
            return [];
        }
        return $this->db->fetchAll(
            'SELECT quotes.*, COALESCE(clients.name, "Sin cliente") as client_name
             FROM quotes
             LEFT JOIN clients ON quotes.client_id = clients.id
             WHERE quotes.company_id = :company_id
             ORDER BY quotes.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function nextNumber(string $prefix, ?int $companyId = null): string
    {
        $companyId = $companyId ?? current_company_id();
        $row = $this->db->fetch('SELECT MAX(id) as max_id FROM quotes WHERE company_id = :company_id', [
            'company_id' => $companyId,
        ]);
        $next = (int)($row['max_id'] ?? 0) + 1;
        return $prefix . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }
}
