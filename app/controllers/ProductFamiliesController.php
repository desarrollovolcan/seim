<?php

class ProductFamiliesController extends Controller
{
    private ProductFamiliesModel $families;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->families = new ProductFamiliesModel($db);
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
        $families = $this->families->active($companyId);
        $this->render('maintainers/product-families/index', [
            'title' => 'Familias de producto',
            'pageTitle' => 'Familias de producto',
            'families' => $families,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireCompany();
        $this->render('maintainers/product-families/create', [
            'title' => 'Nueva familia',
            'pageTitle' => 'Nueva familia',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/product-families/create');
        }
        $this->families->create([
            'company_id' => $companyId,
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'product_families');
        flash('success', 'Familia creada correctamente.');
        $this->redirect('index.php?route=maintainers/product-families');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $family = $this->families->findForCompany($id, $companyId);
        if (!$family) {
            $this->redirect('index.php?route=maintainers/product-families');
        }
        $this->render('maintainers/product-families/edit', [
            'title' => 'Editar familia',
            'pageTitle' => 'Editar familia',
            'family' => $family,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $family = $this->families->findForCompany($id, $companyId);
        if (!$family) {
            $this->redirect('index.php?route=maintainers/product-families');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/product-families/edit&id=' . $id);
        }
        $this->families->update($id, [
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'product_families', $id);
        flash('success', 'Familia actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/product-families');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $family = $this->families->findForCompany($id, $companyId);
        if (!$family) {
            $this->redirect('index.php?route=maintainers/product-families');
        }
        $linked = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM product_subfamilies WHERE family_id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!empty($linked['total'])) {
            flash('error', 'No se puede eliminar porque tiene subfamilias asociadas.');
            $this->redirect('index.php?route=maintainers/product-families');
        }
        $this->db->execute('DELETE FROM product_families WHERE id = :id AND company_id = :company_id', [
            'id' => $id,
            'company_id' => $companyId,
        ]);
        audit($this->db, Auth::user()['id'], 'delete', 'product_families', $id);
        flash('success', 'Familia eliminada correctamente.');
        $this->redirect('index.php?route=maintainers/product-families');
    }
}
