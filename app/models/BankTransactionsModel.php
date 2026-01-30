<?php

class BankTransactionsModel extends Model
{
    protected string $table = 'bank_transactions';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT bt.*, ba.name as account_name
             FROM bank_transactions bt
             JOIN bank_accounts ba ON bt.bank_account_id = ba.id
             WHERE bt.company_id = :company_id
             ORDER BY bt.transaction_date DESC, bt.id DESC',
            ['company_id' => $companyId]
        );
    }
}
