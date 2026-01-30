<?php

class HrPayrollsModel extends Model
{
    protected string $table = 'hr_payrolls';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, e.rut, e.first_name, e.last_name
             FROM hr_payrolls p
             INNER JOIN hr_employees e ON p.employee_id = e.id
             WHERE p.company_id = :company_id
             ORDER BY p.period_start DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM hr_payrolls WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
