<?php

class HrContractsModel extends Model
{
    protected string $table = 'hr_contracts';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT c.*, e.rut, e.first_name, e.last_name,
                    ct.name AS contract_type_name,
                    d.name AS department_name,
                    p.name AS position_name,
                    s.name AS schedule_name
             FROM hr_contracts c
             INNER JOIN hr_employees e ON c.employee_id = e.id
             LEFT JOIN hr_contract_types ct ON c.contract_type_id = ct.id
             LEFT JOIN hr_departments d ON c.department_id = d.id
             LEFT JOIN hr_positions p ON c.position_id = p.id
             LEFT JOIN hr_work_schedules s ON c.schedule_id = s.id
             WHERE c.company_id = :company_id AND c.deleted_at IS NULL
             ORDER BY c.start_date DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM hr_contracts WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function activeForPayroll(int $companyId, string $periodStart, string $periodEnd): array
    {
        return $this->db->fetchAll(
            'SELECT c.*
             FROM hr_contracts c
             WHERE c.company_id = :company_id
               AND c.deleted_at IS NULL
               AND c.status = \"vigente\"
               AND c.start_date <= :period_end
               AND (c.end_date IS NULL OR c.end_date >= :period_start)',
            [
                'company_id' => $companyId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]
        );
    }
}
