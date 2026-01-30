<?php

class HrContractTypesController extends Controller
{
    private HrContractTypesModel $contractTypes;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->contractTypes = new HrContractTypesModel($db);
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

        $this->render('maintainers/hr-contract-types/index', [
            'title' => 'Tipos de contrato',
            'pageTitle' => 'Tipos de contrato',
            'contractTypes' => $this->contractTypes->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-contract-types/create', [
            'title' => 'Nuevo tipo de contrato',
            'pageTitle' => 'Nuevo tipo de contrato',
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
            $this->redirect('index.php?route=maintainers/hr-contract-types/create');
        }

        $this->contractTypes->create([
            'company_id' => $companyId,
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'max_duration_months' => (int)($_POST['max_duration_months'] ?? 0) ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_contract_types');
        flash('success', 'Tipo de contrato creado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-contract-types');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $contractType = $this->contractTypes->findForCompany($id, $companyId);
        if (!$contractType) {
            $this->redirect('index.php?route=maintainers/hr-contract-types');
        }

        $this->render('maintainers/hr-contract-types/edit', [
            'title' => 'Editar tipo de contrato',
            'pageTitle' => 'Editar tipo de contrato',
            'contractType' => $contractType,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $contractType = $this->contractTypes->findForCompany($id, $companyId);
        if (!$contractType) {
            $this->redirect('index.php?route=maintainers/hr-contract-types');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-contract-types/edit&id=' . $id);
        }

        $this->contractTypes->update($id, [
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'max_duration_months' => (int)($_POST['max_duration_months'] ?? 0) ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_contract_types', $id);
        flash('success', 'Tipo de contrato actualizado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-contract-types');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $contractType = $this->contractTypes->findForCompany($id, $companyId);
        if (!$contractType) {
            $this->redirect('index.php?route=maintainers/hr-contract-types');
        }

        $this->contractTypes->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_contract_types', $id);
        flash('success', 'Tipo de contrato eliminado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-contract-types');
    }
}
