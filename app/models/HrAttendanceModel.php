<?php

class HrAttendanceModel extends Model
{
    protected string $table = 'hr_attendance';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT a.*, e.rut, e.first_name, e.last_name
             FROM hr_attendance a
             INNER JOIN hr_employees e ON a.employee_id = e.id
             WHERE a.company_id = :company_id
             ORDER BY a.date DESC, a.check_in DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM hr_attendance WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function findOpenForDate(int $employeeId, int $companyId, string $date): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM hr_attendance
             WHERE employee_id = :employee_id AND company_id = :company_id AND date = :date
             ORDER BY id DESC
             LIMIT 1',
            [
                'employee_id' => $employeeId,
                'company_id' => $companyId,
                'date' => $date,
            ]
        );
    }

    public function calculateWorkedHours(string $startTime, string $endTime): float
    {
        $start = DateTime::createFromFormat('H:i', $startTime);
        $end = DateTime::createFromFormat('H:i', $endTime);
        if (!$start || !$end) {
            return 0.0;
        }
        $interval = $start->diff($end);
        return round(($interval->h * 60 + $interval->i) / 60, 2);
    }
}
