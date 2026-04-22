<?php

class CompaniesController extends Controller
{
    private CompaniesModel $companies;
    private SettingsModel $settings;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->companies = new CompaniesModel($db);
        $this->settings = new SettingsModel($db);
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
        $this->render('companies/create', [
            'title' => 'Nueva Empresa',
            'pageTitle' => 'Nueva Empresa',
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
            'commune' => trim($_POST['commune'] ?? ''),
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
        $companySettings = $this->settings->get('company', [], $id);
        $this->render('companies/edit', [
            'title' => 'Editar Empresa',
            'pageTitle' => 'Editar Empresa',
            'company' => $company,
            'companySettings' => is_array($companySettings) ? $companySettings : [],
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
        $rut = trim($_POST['rut'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $giro = trim($_POST['giro'] ?? '');
        $commune = trim($_POST['commune'] ?? '');

        $companySettings = $this->settings->get('company', [], $id);
        $companySettings = is_array($companySettings) ? $companySettings : [];

        $logoColorResult = upload_company_logo($_FILES['logo_color'] ?? null, 'logo-color');
        if (!empty($logoColorResult['error'])) {
            flash('error', $logoColorResult['error']);
            $this->redirect('index.php?route=companies/edit&id=' . $id);
        }
        $logoBlackResult = upload_company_logo($_FILES['logo_black'] ?? null, 'logo-black');
        if (!empty($logoBlackResult['error'])) {
            flash('error', $logoBlackResult['error']);
            $this->redirect('index.php?route=companies/edit&id=' . $id);
        }

        $companySettingsData = array_merge($companySettings, [
            'name' => $name,
            'rut' => $rut,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'giro' => $giro,
            'commune' => $commune,
            'logo_color' => $companySettings['logo_color'] ?? null,
            'logo_black' => $companySettings['logo_black'] ?? null,
        ]);
        if (!empty($logoColorResult['path'])) {
            $companySettingsData['logo_color'] = $logoColorResult['path'];
        }
        if (!empty($logoBlackResult['path'])) {
            $companySettingsData['logo_black'] = $logoBlackResult['path'];
        }
        $this->companies->update($id, [
            'name' => $name,
            'rut' => $rut,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'giro' => $giro,
            'commune' => $commune,
            'logo_color' => $companySettingsData['logo_color'] ?? null,
            'logo_black' => $companySettingsData['logo_black'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->settings->set('company', $companySettingsData, $id);
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
