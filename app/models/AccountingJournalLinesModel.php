<?php

class AccountingJournalLinesModel extends Model
{
    protected string $table = 'accounting_journal_lines';

    public function byJournal(int $journalId): array
    {
        return $this->db->fetchAll(
            'SELECT ajl.*, aa.code, aa.name as account_name
             FROM accounting_journal_lines ajl
             JOIN accounting_accounts aa ON ajl.account_id = aa.id
             WHERE ajl.journal_id = :journal_id
             ORDER BY ajl.id ASC',
            ['journal_id' => $journalId]
        );
    }
}
