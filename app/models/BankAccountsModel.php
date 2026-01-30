<?php

class BankAccountsModel extends Model
{
    protected string $table = 'bank_accounts';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM bank_accounts WHERE company_id = :company_id ORDER BY id DESC',
            ['company_id' => $companyId]
        );
    }
}
