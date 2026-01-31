<?php

class SuppliersController extends Controller
{
    private SuppliersModel $suppliers;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->suppliers = new SuppliersModel($db);
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
        $suppliers = $this->suppliers->active($companyId);

        $this->render('suppliers/index', [
            'title' => 'Proveedores',
            'pageTitle' => 'Proveedores',
            'suppliers' => $suppliers,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $suppliers = $this->suppliers->active($companyId);
        $communes = chile_communes($this->db);
        $activityCodeOptions = sii_activity_code_options($this->db);
        $this->render('suppliers/create', [
            'title' => 'Nuevo proveedor',
            'pageTitle' => 'Nuevo proveedor',
            'suppliers' => $suppliers,
            'communes' => $communes,
            'activityCodeOptions' => $activityCodeOptions,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $website = trim($_POST['website'] ?? '');
        if (!Validator::required($name)) {
            flash('error', 'El nombre del proveedor es obligatorio.');
            $this->redirect('index.php?route=suppliers/create');
        }
        if (!Validator::optionalEmail($email)) {
            flash('error', 'El email del proveedor no es válido.');
            $this->redirect('index.php?route=suppliers/create');
        }
        if (!Validator::url($website)) {
            flash('error', 'El sitio web no es válido.');
            $this->redirect('index.php?route=suppliers/create');
        }

        $this->suppliers->create([
            'company_id' => $companyId,
            'name' => $name,
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'tax_id' => trim($_POST['tax_id'] ?? ''),
            'email' => $email,
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'giro' => trim($_POST['giro'] ?? ''),
            'activity_code' => trim($_POST['activity_code'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'website' => $website,
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'suppliers');
        flash('success', 'Proveedor creado correctamente.');
        $this->redirect('index.php?route=suppliers');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $supplier = $this->suppliers->findForCompany($id, $companyId);
        if (!$supplier) {
            $this->redirect('index.php?route=suppliers');
        }
        $communes = chile_communes($this->db);
        $activityCodeOptions = sii_activity_code_options($this->db);

        $this->render('suppliers/edit', [
            'title' => 'Editar proveedor',
            'pageTitle' => 'Editar proveedor',
            'supplier' => $supplier,
            'communes' => $communes,
            'activityCodeOptions' => $activityCodeOptions,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $supplier = $this->suppliers->findForCompany($id, $companyId);
        if (!$supplier) {
            flash('error', 'Proveedor no encontrado.');
            $this->redirect('index.php?route=suppliers');
        }
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $website = trim($_POST['website'] ?? '');
        if (!Validator::required($name)) {
            flash('error', 'El nombre del proveedor es obligatorio.');
            $this->redirect('index.php?route=suppliers/edit&id=' . $id);
        }
        if (!Validator::optionalEmail($email)) {
            flash('error', 'El email del proveedor no es válido.');
            $this->redirect('index.php?route=suppliers/edit&id=' . $id);
        }
        if (!Validator::url($website)) {
            flash('error', 'El sitio web no es válido.');
            $this->redirect('index.php?route=suppliers/edit&id=' . $id);
        }

        $this->suppliers->update($id, [
            'name' => $name,
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'tax_id' => trim($_POST['tax_id'] ?? ''),
            'email' => $email,
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'giro' => trim($_POST['giro'] ?? ''),
            'activity_code' => trim($_POST['activity_code'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'website' => $website,
            'notes' => trim($_POST['notes'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'suppliers', $id);
        flash('success', 'Proveedor actualizado correctamente.');
        $this->redirect('index.php?route=suppliers');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $supplier = $this->suppliers->findForCompany($id, $companyId);
        if (!$supplier) {
            flash('error', 'Proveedor no encontrado.');
            $this->redirect('index.php?route=suppliers');
        }

        $this->suppliers->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'suppliers', $id);
        flash('success', 'Proveedor eliminado correctamente.');
        $this->redirect('index.php?route=suppliers');
    }
}
