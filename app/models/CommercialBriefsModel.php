<?php

class CommercialBriefsModel extends Model
{
    protected string $table = 'commercial_briefs';

    public function active(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT * FROM commercial_briefs WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY id DESC',
            ['company_id' => $companyId]
        );
    }
}
