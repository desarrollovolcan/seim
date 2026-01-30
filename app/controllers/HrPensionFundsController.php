<?php

class HrPensionFundsController extends Controller
{
    private HrPensionFundsModel $funds;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->funds = new HrPensionFundsModel($db);
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

        $this->render('maintainers/hr-pension-funds/index', [
            'title' => 'AFP',
            'pageTitle' => 'Administradoras de fondos',
            'funds' => $this->funds->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-pension-funds/create', [
            'title' => 'Nueva AFP',
            'pageTitle' => 'Nueva AFP',
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
            $this->redirect('index.php?route=maintainers/hr-pension-funds/create');
        }

        $this->funds->create([
            'company_id' => $companyId,
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_pension_funds');
        flash('success', 'AFP creada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-pension-funds');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $fund = $this->funds->findForCompany($id, $companyId);
        if (!$fund) {
            $this->redirect('index.php?route=maintainers/hr-pension-funds');
        }

        $this->render('maintainers/hr-pension-funds/edit', [
            'title' => 'Editar AFP',
            'pageTitle' => 'Editar AFP',
            'fund' => $fund,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $fund = $this->funds->findForCompany($id, $companyId);
        if (!$fund) {
            $this->redirect('index.php?route=maintainers/hr-pension-funds');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-pension-funds/edit&id=' . $id);
        }

        $this->funds->update($id, [
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_pension_funds', $id);
        flash('success', 'AFP actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-pension-funds');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $fund = $this->funds->findForCompany($id, $companyId);
        if (!$fund) {
            $this->redirect('index.php?route=maintainers/hr-pension-funds');
        }

        $this->funds->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_pension_funds', $id);
        flash('success', 'AFP eliminada correctamente.');
        $this->redirect('index.php?route=maintainers/hr-pension-funds');
    }
}
