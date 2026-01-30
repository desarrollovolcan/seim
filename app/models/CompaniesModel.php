<?php

class CompaniesModel extends Model
{
    protected string $table = 'companies';

    public function active(): array
    {
        return $this->db->fetchAll('SELECT * FROM companies ORDER BY name');
    }
}
