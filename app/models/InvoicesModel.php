<?php

class InvoicesModel extends Model
{
    protected string $table = 'invoices';

    public function allWithClient(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT invoices.*, clients.name as client_name FROM invoices JOIN clients ON invoices.client_id = clients.id WHERE invoices.deleted_at IS NULL AND invoices.company_id = :company_id ORDER BY invoices.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function nextNumber(string $prefix, ?int $companyId = null): string
    {
        $companyId = $companyId ?? current_company_id();
        $row = $this->db->fetch('SELECT MAX(id) as max_id FROM invoices WHERE company_id = :company_id', [
            'company_id' => $companyId,
        ]);
        $next = (int)($row['max_id'] ?? 0) + 1;
        return $prefix . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }
}
