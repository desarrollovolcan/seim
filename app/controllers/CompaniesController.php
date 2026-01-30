<?php

class CompaniesController extends Controller
{
    private CompaniesModel $companies;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->companies = new CompaniesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companies = $this->companies->active();
        $this->render('companies/index', [
            'title' => 'Empresas',
            'pageTitle' => 'Empresas',
            'companies' => $companies,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $communeCityMap = chile_commune_city_map($this->db);
        $communes = array_keys($communeCityMap);
        $activityCodeOptions = sii_activity_code_options($this->db);
        $this->render('companies/create', [
            'title' => 'Nueva Empresa',
            'pageTitle' => 'Nueva Empresa',
            'communes' => $communes,
            'communeCityMap' => $communeCityMap,
            'activityCodeOptions' => $activityCodeOptions,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=companies/create');
        }
        $data = [
            'name' => $name,
            'rut' => trim($_POST['rut'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'giro' => trim($_POST['giro'] ?? ''),
            'activity_code' => trim($_POST['activity_code'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $companyId = $this->companies->create($data);
        audit($this->db, Auth::user()['id'], 'create', 'companies', $companyId);
        flash('success', 'Empresa creada correctamente.');
        $this->redirect('index.php?route=companies');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $company = $this->companies->find($id);
        if (!$company) {
            $this->redirect('index.php?route=companies');
        }
        $communeCityMap = chile_commune_city_map($this->db);
        $communes = array_keys($communeCityMap);
        $activityCodeOptions = sii_activity_code_options($this->db);
        $this->render('companies/edit', [
            'title' => 'Editar Empresa',
            'pageTitle' => 'Editar Empresa',
            'company' => $company,
            'communes' => $communes,
            'communeCityMap' => $communeCityMap,
            'activityCodeOptions' => $activityCodeOptions,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $company = $this->companies->find($id);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            $this->redirect('index.php?route=companies');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=companies/edit&id=' . $id);
        }
        $this->companies->update($id, [
            'name' => $name,
            'rut' => trim($_POST['rut'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'giro' => trim($_POST['giro'] ?? ''),
            'activity_code' => trim($_POST['activity_code'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'companies', $id);
        flash('success', 'Empresa actualizada correctamente.');
        $this->redirect('index.php?route=companies');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $company = $this->companies->find($id);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            $this->redirect('index.php?route=companies');
        }
        try {
            $this->db->execute('DELETE FROM companies WHERE id = :id', ['id' => $id]);
            audit($this->db, Auth::user()['id'], 'delete', 'companies', $id);
            flash('success', 'Empresa eliminada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete company: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la empresa.');
        }
        $this->redirect('index.php?route=companies');
    }
}
