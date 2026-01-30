<?php

class HrEmployeesModel extends Model
{
    protected string $table = 'hr_employees';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT e.*, d.name AS department_name, p.name AS position_name,
                    hp.name AS health_provider_name, pf.name AS pension_fund_name
             FROM hr_employees e
             LEFT JOIN hr_departments d ON e.department_id = d.id
             LEFT JOIN hr_positions p ON e.position_id = p.id
             LEFT JOIN hr_health_providers hp ON e.health_provider_id = hp.id
             LEFT JOIN hr_pension_funds pf ON e.pension_fund_id = pf.id
             WHERE e.company_id = :company_id AND e.deleted_at IS NULL
             ORDER BY e.last_name ASC, e.first_name ASC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT e.*, d.name AS department_name, p.name AS position_name,
                    hp.name AS health_provider_name, pf.name AS pension_fund_name
             FROM hr_employees e
             LEFT JOIN hr_departments d ON e.department_id = d.id
             LEFT JOIN hr_positions p ON e.position_id = p.id
             LEFT JOIN hr_health_providers hp ON e.health_provider_id = hp.id
             LEFT JOIN hr_pension_funds pf ON e.pension_fund_id = pf.id
             WHERE e.id = :id AND e.company_id = :company_id AND e.deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function findByQrToken(string $token, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM hr_employees WHERE qr_token = :token AND company_id = :company_id AND deleted_at IS NULL',
            ['token' => $token, 'company_id' => $companyId]
        );
    }
}
