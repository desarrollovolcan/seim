<?php

class HonorariosModel extends Model
{
    protected string $table = 'honorarios_documents';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM honorarios_documents WHERE company_id = :company_id ORDER BY issue_date DESC, id DESC',
            ['company_id' => $companyId]
        );
    }
}
