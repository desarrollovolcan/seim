<?php

class HrContractsController extends Controller
{
    private HrContractsModel $contracts;
    private HrEmployeesModel $employees;
    private HrContractTypesModel $contractTypes;
    private HrDepartmentsModel $departments;
    private HrPositionsModel $positions;
    private HrWorkSchedulesModel $schedules;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->contracts = new HrContractsModel($db);
        $this->employees = new HrEmployeesModel($db);
        $this->contractTypes = new HrContractTypesModel($db);
        $this->departments = new HrDepartmentsModel($db);
        $this->positions = new HrPositionsModel($db);
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
        $companyId = $this->requireCompany();
        $contracts = $this->contracts->active($companyId);

        $this->render('hr/contracts/index', [
            'title' => 'Contratos',
            'pageTitle' => 'Contratos laborales',
            'contracts' => $contracts,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('hr/contracts/create', [
            'title' => 'Nuevo contrato',
            'pageTitle' => 'Nuevo contrato',
            'employees' => $this->employees->active($companyId),
            'contractTypes' => $this->contractTypes->active($companyId),
            'departments' => $this->departments->active($companyId),
            'positions' => $this->positions->active($companyId),
            'schedules' => $this->schedules->active($companyId),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $salary = (float)($_POST['salary'] ?? 0);
        if ($employeeId === 0 || $startDate === '' || $salary <= 0) {
            flash('error', 'Selecciona trabajador, fecha de inicio y sueldo base.');
            $this->redirect('index.php?route=hr/contracts/create');
        }

        $employee = $this->employees->findForCompany($employeeId, $companyId);
        if (!$employee) {
            flash('error', 'Trabajador no válido.');
            $this->redirect('index.php?route=hr/contracts/create');
        }

        $contractTypeId = !empty($_POST['contract_type_id']) ? (int)$_POST['contract_type_id'] : null;
        if ($contractTypeId) {
            $contractType = $this->contractTypes->findForCompany($contractTypeId, $companyId);
            if (!$contractType) {
                flash('error', 'Tipo de contrato no válido.');
                $this->redirect('index.php?route=hr/contracts/create');
            }
        }

        $departmentId = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        if ($departmentId) {
            $department = $this->departments->findForCompany($departmentId, $companyId);
            if (!$department) {
                flash('error', 'Departamento no válido.');
                $this->redirect('index.php?route=hr/contracts/create');
            }
        }

        $positionId = !empty($_POST['position_id']) ? (int)$_POST['position_id'] : null;
        if ($positionId) {
            $position = $this->positions->findForCompany($positionId, $companyId);
            if (!$position) {
                flash('error', 'Cargo no válido.');
                $this->redirect('index.php?route=hr/contracts/create');
            }
        }

        $scheduleId = !empty($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : null;
        if ($scheduleId) {
            $schedule = $this->schedules->findForCompany($scheduleId, $companyId);
            if (!$schedule) {
                flash('error', 'Jornada no válida.');
                $this->redirect('index.php?route=hr/contracts/create');
            }
        }

        $this->contracts->create([
            'company_id' => $companyId,
            'employee_id' => $employeeId,
            'contract_type_id' => $contractTypeId,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'schedule_id' => $scheduleId,
            'start_date' => $startDate,
            'end_date' => trim($_POST['end_date'] ?? '') ?: null,
            'salary' => $salary,
            'weekly_hours' => (int)($_POST['weekly_hours'] ?? 45),
            'status' => $_POST['status'] ?? 'vigente',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_contracts');
        flash('success', 'Contrato creado correctamente.');
        $this->redirect('index.php?route=hr/contracts');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $contract = $this->contracts->findForCompany($id, $companyId);
        if (!$contract) {
            $this->redirect('index.php?route=hr/contracts');
        }

        $this->render('hr/contracts/edit', [
            'title' => 'Editar contrato',
            'pageTitle' => 'Editar contrato',
            'contract' => $contract,
            'employees' => $this->employees->active($companyId),
            'contractTypes' => $this->contractTypes->active($companyId),
            'departments' => $this->departments->active($companyId),
            'positions' => $this->positions->active($companyId),
            'schedules' => $this->schedules->active($companyId),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $contract = $this->contracts->findForCompany($id, $companyId);
        if (!$contract) {
            $this->redirect('index.php?route=hr/contracts');
        }

        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $salary = (float)($_POST['salary'] ?? 0);
        if ($employeeId === 0 || $startDate === '' || $salary <= 0) {
            flash('error', 'Selecciona trabajador, fecha de inicio y sueldo base.');
            $this->redirect('index.php?route=hr/contracts/edit&id=' . $id);
        }

        $employee = $this->employees->findForCompany($employeeId, $companyId);
        if (!$employee) {
            flash('error', 'Trabajador no válido.');
            $this->redirect('index.php?route=hr/contracts/edit&id=' . $id);
        }

        $contractTypeId = !empty($_POST['contract_type_id']) ? (int)$_POST['contract_type_id'] : null;
        if ($contractTypeId) {
            $contractType = $this->contractTypes->findForCompany($contractTypeId, $companyId);
            if (!$contractType) {
                flash('error', 'Tipo de contrato no válido.');
                $this->redirect('index.php?route=hr/contracts/edit&id=' . $id);
            }
        }

        $departmentId = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        if ($departmentId) {
            $department = $this->departments->findForCompany($departmentId, $companyId);
            if (!$department) {
                flash('error', 'Departamento no válido.');
                $this->redirect('index.php?route=hr/contracts/edit&id=' . $id);
            }
        }

        $positionId = !empty($_POST['position_id']) ? (int)$_POST['position_id'] : null;
        if ($positionId) {
            $position = $this->positions->findForCompany($positionId, $companyId);
            if (!$position) {
                flash('error', 'Cargo no válido.');
                $this->redirect('index.php?route=hr/contracts/edit&id=' . $id);
            }
        }

        $scheduleId = !empty($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : null;
        if ($scheduleId) {
            $schedule = $this->schedules->findForCompany($scheduleId, $companyId);
            if (!$schedule) {
                flash('error', 'Jornada no válida.');
                $this->redirect('index.php?route=hr/contracts/edit&id=' . $id);
            }
        }

        $this->contracts->update($id, [
            'employee_id' => $employeeId,
            'contract_type_id' => $contractTypeId,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'schedule_id' => $scheduleId,
            'start_date' => $startDate,
            'end_date' => trim($_POST['end_date'] ?? '') ?: null,
            'salary' => $salary,
            'weekly_hours' => (int)($_POST['weekly_hours'] ?? 45),
            'status' => $_POST['status'] ?? 'vigente',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_contracts', $id);
        flash('success', 'Contrato actualizado correctamente.');
        $this->redirect('index.php?route=hr/contracts');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $contract = $this->contracts->findForCompany($id, $companyId);
        if (!$contract) {
            $this->redirect('index.php?route=hr/contracts');
        }

        $this->contracts->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_contracts', $id);
        flash('success', 'Contrato eliminado correctamente.');
        $this->redirect('index.php?route=hr/contracts');
    }

    public function bulkCreate(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('hr/contracts/bulk', [
            'title' => 'Contratos masivos',
            'pageTitle' => 'Generación masiva de contratos',
            'employees' => $this->employees->active($companyId),
            'contractTypes' => $this->contractTypes->active($companyId),
            'departments' => $this->departments->active($companyId),
            'positions' => $this->positions->active($companyId),
            'schedules' => $this->schedules->active($companyId),
        ]);
    }

    public function bulkStore(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $employeeIds = array_map('intval', $_POST['employee_ids'] ?? []);
        $employeeIds = array_values(array_filter($employeeIds));
        if (empty($employeeIds)) {
            flash('error', 'Selecciona al menos un trabajador.');
            $this->redirect('index.php?route=hr/contracts/bulk');
        }

        $startDate = trim($_POST['start_date'] ?? '');
        $salary = (float)($_POST['salary'] ?? 0);
        if ($startDate === '' || $salary <= 0) {
            flash('error', 'Completa fecha de inicio y sueldo base.');
            $this->redirect('index.php?route=hr/contracts/bulk');
        }

        $contractTypeId = !empty($_POST['contract_type_id']) ? (int)$_POST['contract_type_id'] : null;
        $departmentId = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $positionId = !empty($_POST['position_id']) ? (int)$_POST['position_id'] : null;
        $scheduleId = !empty($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : null;

        foreach ($employeeIds as $employeeId) {
            $employee = $this->employees->findForCompany($employeeId, $companyId);
            if (!$employee) {
                continue;
            }
            $this->contracts->create([
                'company_id' => $companyId,
                'employee_id' => $employeeId,
                'contract_type_id' => $contractTypeId,
                'department_id' => $departmentId,
                'position_id' => $positionId,
                'schedule_id' => $scheduleId,
                'start_date' => $startDate,
                'end_date' => trim($_POST['end_date'] ?? '') ?: null,
                'salary' => $salary,
                'weekly_hours' => (int)($_POST['weekly_hours'] ?? 45),
                'status' => $_POST['status'] ?? 'vigente',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        audit($this->db, Auth::user()['id'], 'create', 'hr_contracts_bulk');
        flash('success', 'Contratos generados correctamente.');
        $this->redirect('index.php?route=hr/contracts');
    }
}
