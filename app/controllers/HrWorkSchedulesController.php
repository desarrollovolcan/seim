<?php

class HrWorkSchedulesController extends Controller
{
    private HrWorkSchedulesModel $schedules;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->schedules = new HrWorkSchedulesModel($db);
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
        $this->requireRole('admin');
        $companyId = $this->requireCompany();

        $this->render('maintainers/hr-work-schedules/index', [
            'title' => 'Jornadas',
            'pageTitle' => 'Jornadas laborales',
            'schedules' => $this->schedules->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-work-schedules/create', [
            'title' => 'Nueva jornada',
            'pageTitle' => 'Nueva jornada',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-work-schedules/create');
        }

        $this->schedules->create([
            'company_id' => $companyId,
            'name' => $name,
            'weekly_hours' => (int)($_POST['weekly_hours'] ?? 45),
            'start_time' => trim($_POST['start_time'] ?? ''),
            'end_time' => trim($_POST['end_time'] ?? ''),
            'lunch_break_minutes' => (int)($_POST['lunch_break_minutes'] ?? 60),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_work_schedules');
        flash('success', 'Jornada creada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-work-schedules');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $schedule = $this->schedules->findForCompany($id, $companyId);
        if (!$schedule) {
            $this->redirect('index.php?route=maintainers/hr-work-schedules');
        }

        $this->render('maintainers/hr-work-schedules/edit', [
            'title' => 'Editar jornada',
            'pageTitle' => 'Editar jornada',
            'schedule' => $schedule,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $schedule = $this->schedules->findForCompany($id, $companyId);
        if (!$schedule) {
            $this->redirect('index.php?route=maintainers/hr-work-schedules');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-work-schedules/edit&id=' . $id);
        }

        $this->schedules->update($id, [
            'name' => $name,
            'weekly_hours' => (int)($_POST['weekly_hours'] ?? 45),
            'start_time' => trim($_POST['start_time'] ?? ''),
            'end_time' => trim($_POST['end_time'] ?? ''),
            'lunch_break_minutes' => (int)($_POST['lunch_break_minutes'] ?? 60),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_work_schedules', $id);
        flash('success', 'Jornada actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-work-schedules');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $schedule = $this->schedules->findForCompany($id, $companyId);
        if (!$schedule) {
            $this->redirect('index.php?route=maintainers/hr-work-schedules');
        }

        $this->schedules->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_work_schedules', $id);
        flash('success', 'Jornada eliminada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-work-schedules');
    }
}
