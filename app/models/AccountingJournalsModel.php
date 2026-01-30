<?php

class AccountingJournalsModel extends Model
{
    protected string $table = 'accounting_journals';

    public function listWithTotals(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT aj.*, COALESCE(SUM(ajl.debit), 0) as total_debit, COALESCE(SUM(ajl.credit), 0) as total_credit
             FROM accounting_journals aj
             LEFT JOIN accounting_journal_lines ajl ON aj.id = ajl.journal_id
             WHERE aj.company_id = :company_id
             GROUP BY aj.id
             ORDER BY aj.entry_date DESC, aj.id DESC',
            ['company_id' => $companyId]
        );
    }
}
