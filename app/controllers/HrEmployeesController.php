<?php

class HrEmployeesController extends Controller
{
    private HrEmployeesModel $employees;
    private HrDepartmentsModel $departments;
    private HrPositionsModel $positions;
    private HrHealthProvidersModel $healthProviders;
    private HrPensionFundsModel $pensionFunds;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->employees = new HrEmployeesModel($db);
        $this->departments = new HrDepartmentsModel($db);
        $this->positions = new HrPositionsModel($db);
        $this->healthProviders = new HrHealthProvidersModel($db);
        $this->pensionFunds = new HrPensionFundsModel($db);
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
        $employees = $this->employees->active($companyId);

        $this->render('hr/employees/index', [
            'title' => 'Trabajadores',
            'pageTitle' => 'Registro de trabajadores',
            'employees' => $employees,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $departments = $this->departments->active($companyId);
        $positions = $this->positions->active($companyId);

        $this->render('hr/employees/create', [
            'title' => 'Nuevo trabajador',
            'pageTitle' => 'Nuevo trabajador',
            'departments' => $departments,
            'positions' => $positions,
            'healthProviders' => $this->healthProviders->active($companyId),
            'pensionFunds' => $this->pensionFunds->active($companyId),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $rut = trim($_POST['rut'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $hireDate = trim($_POST['hire_date'] ?? '');

        if ($rut === '' || $firstName === '' || $lastName === '' || $hireDate === '') {
            flash('error', 'Completa RUT, nombre, apellido y fecha de ingreso.');
            $this->redirect('index.php?route=hr/employees/create');
        }

        $departmentId = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $positionId = !empty($_POST['position_id']) ? (int)$_POST['position_id'] : null;

        if ($departmentId) {
            $department = $this->departments->findForCompany($departmentId, $companyId);
            if (!$department) {
                flash('error', 'Departamento no válido.');
                $this->redirect('index.php?route=hr/employees/create');
            }
        }
        if ($positionId) {
            $position = $this->positions->findForCompany($positionId, $companyId);
            if (!$position) {
                flash('error', 'Cargo no válido.');
                $this->redirect('index.php?route=hr/employees/create');
            }
        }
        $healthProviderId = !empty($_POST['health_provider_id']) ? (int)$_POST['health_provider_id'] : null;
        $healthProviderName = '';
        if ($healthProviderId) {
            $provider = $this->healthProviders->findForCompany($healthProviderId, $companyId);
            if (!$provider) {
                flash('error', 'Institución de salud no válida.');
                $this->redirect('index.php?route=hr/employees/create');
            }
            $healthProviderName = $provider['name'] ?? '';
        }
        $pensionFundId = !empty($_POST['pension_fund_id']) ? (int)$_POST['pension_fund_id'] : null;
        $pensionFundName = '';
        if ($pensionFundId) {
            $fund = $this->pensionFunds->findForCompany($pensionFundId, $companyId);
            if (!$fund) {
                flash('error', 'AFP no válida.');
                $this->redirect('index.php?route=hr/employees/create');
            }
            $pensionFundName = $fund['name'] ?? '';
        }
        $qrToken = bin2hex(random_bytes(8));

        $this->employees->create([
            'company_id' => $companyId,
            'rut' => $rut,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'nationality' => trim($_POST['nationality'] ?? ''),
            'birth_date' => trim($_POST['birth_date'] ?? '') ?: null,
            'civil_status' => trim($_POST['civil_status'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'hire_date' => $hireDate,
            'termination_date' => trim($_POST['termination_date'] ?? '') ?: null,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'health_provider_id' => $healthProviderId,
            'health_provider' => $healthProviderName,
            'health_plan' => trim($_POST['health_plan'] ?? ''),
            'pension_fund_id' => $pensionFundId,
            'pension_fund' => $pensionFundName,
            'pension_rate' => (float)($_POST['pension_rate'] ?? 10),
            'health_rate' => (float)($_POST['health_rate'] ?? 7),
            'unemployment_rate' => (float)($_POST['unemployment_rate'] ?? 0.6),
            'dependents_count' => (int)($_POST['dependents_count'] ?? 0),
            'payment_method' => trim($_POST['payment_method'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'bank_account_type' => trim($_POST['bank_account_type'] ?? ''),
            'bank_account_number' => trim($_POST['bank_account_number'] ?? ''),
            'qr_token' => $qrToken,
            'face_descriptor' => trim($_POST['face_descriptor'] ?? ''),
            'status' => $_POST['status'] ?? 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_employees');
        flash('success', 'Trabajador creado correctamente.');
        $this->redirect('index.php?route=hr/employees');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $employee = $this->employees->findForCompany($id, $companyId);
        if (!$employee) {
            $this->redirect('index.php?route=hr/employees');
        }
        $departments = $this->departments->active($companyId);
        $positions = $this->positions->active($companyId);

        $this->render('hr/employees/edit', [
            'title' => 'Editar trabajador',
            'pageTitle' => 'Editar trabajador',
            'employee' => $employee,
            'departments' => $departments,
            'positions' => $positions,
            'healthProviders' => $this->healthProviders->active($companyId),
            'pensionFunds' => $this->pensionFunds->active($companyId),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $employee = $this->employees->findForCompany($id, $companyId);
        if (!$employee) {
            $this->redirect('index.php?route=hr/employees');
        }

        $rut = trim($_POST['rut'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $hireDate = trim($_POST['hire_date'] ?? '');
        if ($rut === '' || $firstName === '' || $lastName === '' || $hireDate === '') {
            flash('error', 'Completa RUT, nombre, apellido y fecha de ingreso.');
            $this->redirect('index.php?route=hr/employees/edit&id=' . $id);
        }

        $departmentId = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $positionId = !empty($_POST['position_id']) ? (int)$_POST['position_id'] : null;
        if ($departmentId) {
            $department = $this->departments->findForCompany($departmentId, $companyId);
            if (!$department) {
                flash('error', 'Departamento no válido.');
                $this->redirect('index.php?route=hr/employees/edit&id=' . $id);
            }
        }
        if ($positionId) {
            $position = $this->positions->findForCompany($positionId, $companyId);
            if (!$position) {
                flash('error', 'Cargo no válido.');
                $this->redirect('index.php?route=hr/employees/edit&id=' . $id);
            }
        }
        $healthProviderId = !empty($_POST['health_provider_id']) ? (int)$_POST['health_provider_id'] : null;
        $healthProviderName = '';
        if ($healthProviderId) {
            $provider = $this->healthProviders->findForCompany($healthProviderId, $companyId);
            if (!$provider) {
                flash('error', 'Institución de salud no válida.');
                $this->redirect('index.php?route=hr/employees/edit&id=' . $id);
            }
            $healthProviderName = $provider['name'] ?? '';
        }
        $pensionFundId = !empty($_POST['pension_fund_id']) ? (int)$_POST['pension_fund_id'] : null;
        $pensionFundName = '';
        if ($pensionFundId) {
            $fund = $this->pensionFunds->findForCompany($pensionFundId, $companyId);
            if (!$fund) {
                flash('error', 'AFP no válida.');
                $this->redirect('index.php?route=hr/employees/edit&id=' . $id);
            }
            $pensionFundName = $fund['name'] ?? '';
        }

        $this->employees->update($id, [
            'rut' => $rut,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'nationality' => trim($_POST['nationality'] ?? ''),
            'birth_date' => trim($_POST['birth_date'] ?? '') ?: null,
            'civil_status' => trim($_POST['civil_status'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'hire_date' => $hireDate,
            'termination_date' => trim($_POST['termination_date'] ?? '') ?: null,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'health_provider_id' => $healthProviderId,
            'health_provider' => $healthProviderName,
            'health_plan' => trim($_POST['health_plan'] ?? ''),
            'pension_fund_id' => $pensionFundId,
            'pension_fund' => $pensionFundName,
            'pension_rate' => (float)($_POST['pension_rate'] ?? 10),
            'health_rate' => (float)($_POST['health_rate'] ?? 7),
            'unemployment_rate' => (float)($_POST['unemployment_rate'] ?? 0.6),
            'dependents_count' => (int)($_POST['dependents_count'] ?? 0),
            'payment_method' => trim($_POST['payment_method'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'bank_account_type' => trim($_POST['bank_account_type'] ?? ''),
            'bank_account_number' => trim($_POST['bank_account_number'] ?? ''),
            'qr_token' => trim($_POST['qr_token'] ?? ($employee['qr_token'] ?? '')),
            'face_descriptor' => trim($_POST['face_descriptor'] ?? ($employee['face_descriptor'] ?? '')),
            'status' => $_POST['status'] ?? 'activo',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_employees', $id);
        flash('success', 'Trabajador actualizado correctamente.');
        $this->redirect('index.php?route=hr/employees');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $employee = $this->employees->findForCompany($id, $companyId);
        if (!$employee) {
            $this->redirect('index.php?route=hr/employees');
        }

        $this->employees->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_employees', $id);
        flash('success', 'Trabajador eliminado correctamente.');
        $this->redirect('index.php?route=hr/employees');
    }

    public function card(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $employee = $this->employees->findForCompany($id, $companyId);
        if (!$employee) {
            $this->redirect('index.php?route=hr/employees');
        }
        if (empty($employee['qr_token'])) {
            $token = bin2hex(random_bytes(8));
            $this->employees->update($id, [
                'qr_token' => $token,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $employee['qr_token'] = $token;
        }

        $this->render('hr/employees/card', [
            'title' => 'Credencial de trabajador',
            'pageTitle' => 'Credencial de trabajador',
            'employee' => $employee,
        ]);
    }
}
