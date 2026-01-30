<?php

class HrAttendanceController extends Controller
{
    private HrAttendanceModel $attendance;
    private HrEmployeesModel $employees;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->attendance = new HrAttendanceModel($db);
        $this->employees = new HrEmployeesModel($db);
    }

    private function requireCompany(): int
    {
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        return (int)$companyId;
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('hr/attendance/index', [
            'title' => 'Asistencia',
            'pageTitle' => 'Control de asistencia',
            'attendance' => $this->attendance->byCompany($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('hr/attendance/create', [
            'title' => 'Nuevo registro',
            'pageTitle' => 'Registrar asistencia',
            'employees' => $this->employees->active($companyId),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $date = trim($_POST['date'] ?? '');
        if ($employeeId === 0 || $date === '') {
            flash('error', 'Selecciona trabajador y fecha.');
            $this->redirect('index.php?route=hr/attendance/create');
        }

        $employee = $this->employees->findForCompany($employeeId, $companyId);
        if (!$employee) {
            flash('error', 'Trabajador no válido.');
            $this->redirect('index.php?route=hr/attendance/create');
        }

        $checkIn = trim($_POST['check_in'] ?? '');
        $checkOut = trim($_POST['check_out'] ?? '');
        $workedHours = null;
        if ($checkIn !== '' && $checkOut !== '') {
            $start = DateTime::createFromFormat('H:i', $checkIn);
            $end = DateTime::createFromFormat('H:i', $checkOut);
            if ($start && $end) {
                $interval = $start->diff($end);
                $workedHours = round(($interval->h * 60 + $interval->i) / 60, 2);
            }
        }

        $this->attendance->create([
            'company_id' => $companyId,
            'employee_id' => $employeeId,
            'date' => $date,
            'check_in' => $checkIn ?: null,
            'check_out' => $checkOut ?: null,
            'worked_hours' => $workedHours,
            'overtime_hours' => (float)($_POST['overtime_hours'] ?? 0),
            'absence_type' => trim($_POST['absence_type'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_attendance');
        flash('success', 'Asistencia registrada correctamente.');
        $this->redirect('index.php?route=hr/attendance');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $record = $this->attendance->findForCompany($id, $companyId);
        if (!$record) {
            $this->redirect('index.php?route=hr/attendance');
        }

        $this->db->execute('DELETE FROM hr_attendance WHERE id = :id AND company_id = :company_id', [
            'id' => $id,
            'company_id' => $companyId,
        ]);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_attendance', $id);
        flash('success', 'Registro eliminado correctamente.');
        $this->redirect('index.php?route=hr/attendance');
    }
}
