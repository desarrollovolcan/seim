<?php

class TaxPeriodsModel extends Model
{
    protected string $table = 'tax_periods';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM tax_periods WHERE company_id = :company_id ORDER BY period DESC',
            ['company_id' => $companyId]
        );
    }
}
