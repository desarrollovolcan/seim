<?php

class FixedAssetsModel extends Model
{
    protected string $table = 'fixed_assets';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM fixed_assets WHERE company_id = :company_id ORDER BY acquisition_date DESC, id DESC',
            ['company_id' => $companyId]
        );
    }
}
