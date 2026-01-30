<?php

class ProductSubfamiliesController extends Controller
{
    private ProductSubfamiliesModel $subfamilies;
    private ProductFamiliesModel $families;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->subfamilies = new ProductSubfamiliesModel($db);
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
        $subfamilies = $this->subfamilies->active($companyId);
        $families = $this->families->active($companyId);
        $familiesById = [];
        foreach ($families as $family) {
            $familiesById[(int)$family['id']] = $family;
        }
        $this->render('maintainers/product-subfamilies/index', [
            'title' => 'Subfamilias de producto',
            'pageTitle' => 'Subfamilias de producto',
            'subfamilies' => $subfamilies,
            'familiesById' => $familiesById,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $families = $this->families->active($companyId);
        $this->render('maintainers/product-subfamilies/create', [
            'title' => 'Nueva subfamilia',
            'pageTitle' => 'Nueva subfamilia',
            'families' => $families,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $name = trim($_POST['name'] ?? '');
        $familyId = (int)($_POST['family_id'] ?? 0);
        $family = $familyId ? $this->families->findForCompany($familyId, $companyId) : null;
        if (!$family) {
            flash('error', 'Selecciona una familia válida.');
            $this->redirect('index.php?route=maintainers/product-subfamilies/create');
        }
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/product-subfamilies/create');
        }
        $this->subfamilies->create([
            'company_id' => $companyId,
            'family_id' => $familyId,
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'product_subfamilies');
        flash('success', 'Subfamilia creada correctamente.');
        $this->redirect('index.php?route=maintainers/product-subfamilies');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $subfamily = $this->subfamilies->findForCompany($id, $companyId);
        if (!$subfamily) {
            $this->redirect('index.php?route=maintainers/product-subfamilies');
        }
        $families = $this->families->active($companyId);
        $this->render('maintainers/product-subfamilies/edit', [
            'title' => 'Editar subfamilia',
            'pageTitle' => 'Editar subfamilia',
            'subfamily' => $subfamily,
            'families' => $families,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $subfamily = $this->subfamilies->findForCompany($id, $companyId);
        if (!$subfamily) {
            $this->redirect('index.php?route=maintainers/product-subfamilies');
        }
        $name = trim($_POST['name'] ?? '');
        $familyId = (int)($_POST['family_id'] ?? 0);
        $family = $familyId ? $this->families->findForCompany($familyId, $companyId) : null;
        if (!$family) {
            flash('error', 'Selecciona una familia válida.');
            $this->redirect('index.php?route=maintainers/product-subfamilies/edit&id=' . $id);
        }
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/product-subfamilies/edit&id=' . $id);
        }
        $this->subfamilies->update($id, [
            'family_id' => $familyId,
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'product_subfamilies', $id);
        flash('success', 'Subfamilia actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/product-subfamilies');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $subfamily = $this->subfamilies->findForCompany($id, $companyId);
        if (!$subfamily) {
            $this->redirect('index.php?route=maintainers/product-subfamilies');
        }
        $linked = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM products WHERE subfamily_id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!empty($linked['total'])) {
            flash('error', 'No se puede eliminar porque hay productos asociados.');
            $this->redirect('index.php?route=maintainers/product-subfamilies');
        }
        $this->db->execute('DELETE FROM product_subfamilies WHERE id = :id AND company_id = :company_id', [
            'id' => $id,
            'company_id' => $companyId,
        ]);
        audit($this->db, Auth::user()['id'], 'delete', 'product_subfamilies', $id);
        flash('success', 'Subfamilia eliminada correctamente.');
        $this->redirect('index.php?route=maintainers/product-subfamilies');
    }
}
