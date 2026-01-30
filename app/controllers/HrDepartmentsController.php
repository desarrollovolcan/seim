<?php

class HrDepartmentsController extends Controller
{
    private HrDepartmentsModel $departments;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->departments = new HrDepartmentsModel($db);
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

        $this->render('maintainers/hr-departments/index', [
            'title' => 'Departamentos',
            'pageTitle' => 'Departamentos',
            'departments' => $this->departments->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-departments/create', [
            'title' => 'Nuevo departamento',
            'pageTitle' => 'Nuevo departamento',
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
            $this->redirect('index.php?route=maintainers/hr-departments/create');
        }

        $this->departments->create([
            'company_id' => $companyId,
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_departments');
        flash('success', 'Departamento creado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-departments');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $department = $this->departments->findForCompany($id, $companyId);
        if (!$department) {
            $this->redirect('index.php?route=maintainers/hr-departments');
        }

        $this->render('maintainers/hr-departments/edit', [
            'title' => 'Editar departamento',
            'pageTitle' => 'Editar departamento',
            'department' => $department,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $department = $this->departments->findForCompany($id, $companyId);
        if (!$department) {
            $this->redirect('index.php?route=maintainers/hr-departments');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-departments/edit&id=' . $id);
        }

        $this->departments->update($id, [
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_departments', $id);
        flash('success', 'Departamento actualizado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-departments');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $department = $this->departments->findForCompany($id, $companyId);
        if (!$department) {
            $this->redirect('index.php?route=maintainers/hr-departments');
        }

        $this->departments->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_departments', $id);
        flash('success', 'Departamento eliminado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-departments');
    }
}
