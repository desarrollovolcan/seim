<?php

class HrPositionsController extends Controller
{
    private HrPositionsModel $positions;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->positions = new HrPositionsModel($db);
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

        $this->render('maintainers/hr-positions/index', [
            'title' => 'Cargos',
            'pageTitle' => 'Cargos',
            'positions' => $this->positions->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-positions/create', [
            'title' => 'Nuevo cargo',
            'pageTitle' => 'Nuevo cargo',
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
            $this->redirect('index.php?route=maintainers/hr-positions/create');
        }

        $this->positions->create([
            'company_id' => $companyId,
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_positions');
        flash('success', 'Cargo creado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-positions');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $position = $this->positions->findForCompany($id, $companyId);
        if (!$position) {
            $this->redirect('index.php?route=maintainers/hr-positions');
        }

        $this->render('maintainers/hr-positions/edit', [
            'title' => 'Editar cargo',
            'pageTitle' => 'Editar cargo',
            'position' => $position,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $position = $this->positions->findForCompany($id, $companyId);
        if (!$position) {
            $this->redirect('index.php?route=maintainers/hr-positions');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-positions/edit&id=' . $id);
        }

        $this->positions->update($id, [
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_positions', $id);
        flash('success', 'Cargo actualizado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-positions');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $position = $this->positions->findForCompany($id, $companyId);
        if (!$position) {
            $this->redirect('index.php?route=maintainers/hr-positions');
        }

        $this->positions->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_positions', $id);
        flash('success', 'Cargo eliminado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-positions');
    }
}
