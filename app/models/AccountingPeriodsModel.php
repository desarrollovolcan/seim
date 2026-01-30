<?php

class AccountingPeriodsModel extends Model
{
    protected string $table = 'accounting_periods';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM accounting_periods WHERE company_id = :company_id ORDER BY period DESC',
            ['company_id' => $companyId]
        );
    }
}
