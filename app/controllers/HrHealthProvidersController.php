<?php

class HrHealthProvidersController extends Controller
{
    private HrHealthProvidersModel $providers;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->providers = new HrHealthProvidersModel($db);
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

        $this->render('maintainers/hr-health-providers/index', [
            'title' => 'Salud',
            'pageTitle' => 'Instituciones de salud',
            'providers' => $this->providers->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-health-providers/create', [
            'title' => 'Nueva institución de salud',
            'pageTitle' => 'Nueva institución de salud',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['name'] ?? '');
        $type = $_POST['provider_type'] ?? 'fonasa';
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-health-providers/create');
        }

        $this->providers->create([
            'company_id' => $companyId,
            'name' => $name,
            'provider_type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_health_providers');
        flash('success', 'Institución creada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-health-providers');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $provider = $this->providers->findForCompany($id, $companyId);
        if (!$provider) {
            $this->redirect('index.php?route=maintainers/hr-health-providers');
        }

        $this->render('maintainers/hr-health-providers/edit', [
            'title' => 'Editar institución de salud',
            'pageTitle' => 'Editar institución de salud',
            'provider' => $provider,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $provider = $this->providers->findForCompany($id, $companyId);
        if (!$provider) {
            $this->redirect('index.php?route=maintainers/hr-health-providers');
        }

        $name = trim($_POST['name'] ?? '');
        $type = $_POST['provider_type'] ?? 'fonasa';
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-health-providers/edit&id=' . $id);
        }

        $this->providers->update($id, [
            'name' => $name,
            'provider_type' => $type,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_health_providers', $id);
        flash('success', 'Institución actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-health-providers');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $provider = $this->providers->findForCompany($id, $companyId);
        if (!$provider) {
            $this->redirect('index.php?route=maintainers/hr-health-providers');
        }

        $this->providers->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_health_providers', $id);
        flash('success', 'Institución eliminada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-health-providers');
    }
}
