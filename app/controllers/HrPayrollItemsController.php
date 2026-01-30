<?php

class HrPayrollItemsController extends Controller
{
    private HrPayrollItemsModel $items;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->items = new HrPayrollItemsModel($db);
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

        $this->render('maintainers/hr-payroll-items/index', [
            'title' => 'Ítems de remuneración',
            'pageTitle' => 'Ítems de remuneración',
            'items' => $this->items->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->requireCompany();

        $this->render('maintainers/hr-payroll-items/create', [
            'title' => 'Nuevo ítem',
            'pageTitle' => 'Nuevo ítem de remuneración',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['name'] ?? '');
        $itemType = $_POST['item_type'] ?? 'haber';
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-payroll-items/create');
        }

        $this->items->create([
            'company_id' => $companyId,
            'name' => $name,
            'item_type' => $itemType,
            'taxable' => !empty($_POST['taxable']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_payroll_items');
        flash('success', 'Ítem creado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-payroll-items');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $item = $this->items->findForCompany($id, $companyId);
        if (!$item) {
            $this->redirect('index.php?route=maintainers/hr-payroll-items');
        }

        $this->render('maintainers/hr-payroll-items/edit', [
            'title' => 'Editar ítem',
            'pageTitle' => 'Editar ítem de remuneración',
            'item' => $item,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $item = $this->items->findForCompany($id, $companyId);
        if (!$item) {
            $this->redirect('index.php?route=maintainers/hr-payroll-items');
        }

        $name = trim($_POST['name'] ?? '');
        $itemType = $_POST['item_type'] ?? 'haber';
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/hr-payroll-items/edit&id=' . $id);
        }

        $this->items->update($id, [
            'name' => $name,
            'item_type' => $itemType,
            'taxable' => !empty($_POST['taxable']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'hr_payroll_items', $id);
        flash('success', 'Ítem actualizado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-payroll-items');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $item = $this->items->findForCompany($id, $companyId);
        if (!$item) {
            $this->redirect('index.php?route=maintainers/hr-payroll-items');
        }

        $this->items->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_payroll_items', $id);
        flash('success', 'Ítem eliminado correctamente.');
        $this->redirect('index.php?route=maintainers/hr-payroll-items');
    }
}
