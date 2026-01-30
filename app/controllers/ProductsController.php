<?php

class ProductsController extends Controller
{
    private ProductsModel $products;
    private SuppliersModel $suppliers;
    private ProductFamiliesModel $families;
    private ProductSubfamiliesModel $subfamilies;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->products = new ProductsModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->families = new ProductFamiliesModel($db);
        $this->subfamilies = new ProductSubfamiliesModel($db);
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
        $products = $this->products->active($companyId);

        $this->render('products/index', [
            'title' => 'Productos',
            'pageTitle' => 'Inventario de productos',
            'products' => $products,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $suppliers = $this->suppliers->active($companyId);
        $families = $this->families->active($companyId);
        $subfamilies = $this->subfamilies->active($companyId);

        $this->render('products/create', [
            'title' => 'Nuevo producto',
            'pageTitle' => 'Nuevo producto',
            'suppliers' => $suppliers,
            'families' => $families,
            'subfamilies' => $subfamilies,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('index.php?route=products/create');
        }
        if ($supplierId) {
            $supplier = $this->suppliers->findForCompany($supplierId, $companyId);
            if (!$supplier) {
                flash('error', 'Proveedor no válido para esta empresa.');
                $this->redirect('index.php?route=products/create');
            }
        }
        $familyId = !empty($_POST['family_id']) ? (int)$_POST['family_id'] : null;
        $subfamilyId = !empty($_POST['subfamily_id']) ? (int)$_POST['subfamily_id'] : null;
        if ($familyId) {
            $family = $this->families->findForCompany($familyId, $companyId);
            if (!$family) {
                flash('error', 'Familia no válida.');
                $this->redirect('index.php?route=products/create');
            }
        }
        if ($subfamilyId) {
            $subfamily = $this->subfamilies->findForCompany($subfamilyId, $companyId);
            if (!$subfamily) {
                flash('error', 'Subfamilia no válida.');
                $this->redirect('index.php?route=products/create');
            }
            if ($familyId && (int)$subfamily['family_id'] !== $familyId) {
                flash('error', 'La subfamilia no pertenece a la familia seleccionada.');
                $this->redirect('index.php?route=products/create');
            }
            if (!$familyId) {
                $familyId = (int)$subfamily['family_id'];
            }
        }

        $this->products->create([
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
            'family_id' => $familyId,
            'subfamily_id' => $subfamilyId,
            'name' => $name,
            'sku' => trim($_POST['sku'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cost' => (float)($_POST['cost'] ?? 0),
            'stock' => (int)($_POST['stock'] ?? 0),
            'stock_min' => (int)($_POST['stock_min'] ?? 0),
            'status' => $_POST['status'] ?? 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'products');
        flash('success', 'Producto creado correctamente.');
        $this->redirect('index.php?route=products');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->products->findForCompany($id, $companyId);
        if (!$product) {
            $this->redirect('index.php?route=products');
        }
        $suppliers = $this->suppliers->active($companyId);
        $families = $this->families->active($companyId);
        $subfamilies = $this->subfamilies->active($companyId);

        $this->render('products/edit', [
            'title' => 'Editar producto',
            'pageTitle' => 'Editar producto',
            'product' => $product,
            'suppliers' => $suppliers,
            'families' => $families,
            'subfamilies' => $subfamilies,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $product = $this->products->findForCompany($id, $companyId);
        if (!$product) {
            flash('error', 'Producto no encontrado.');
            $this->redirect('index.php?route=products');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('index.php?route=products/edit&id=' . $id);
        }
        $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
        if ($supplierId) {
            $supplier = $this->suppliers->findForCompany($supplierId, $companyId);
            if (!$supplier) {
                flash('error', 'Proveedor no válido para esta empresa.');
                $this->redirect('index.php?route=products/edit&id=' . $id);
            }
        }
        $familyId = !empty($_POST['family_id']) ? (int)$_POST['family_id'] : null;
        $subfamilyId = !empty($_POST['subfamily_id']) ? (int)$_POST['subfamily_id'] : null;
        if ($familyId) {
            $family = $this->families->findForCompany($familyId, $companyId);
            if (!$family) {
                flash('error', 'Familia no válida.');
                $this->redirect('index.php?route=products/edit&id=' . $id);
            }
        }
        if ($subfamilyId) {
            $subfamily = $this->subfamilies->findForCompany($subfamilyId, $companyId);
            if (!$subfamily) {
                flash('error', 'Subfamilia no válida.');
                $this->redirect('index.php?route=products/edit&id=' . $id);
            }
            if ($familyId && (int)$subfamily['family_id'] !== $familyId) {
                flash('error', 'La subfamilia no pertenece a la familia seleccionada.');
                $this->redirect('index.php?route=products/edit&id=' . $id);
            }
            if (!$familyId) {
                $familyId = (int)$subfamily['family_id'];
            }
        }

        $this->products->update($id, [
            'supplier_id' => $supplierId,
            'family_id' => $familyId,
            'subfamily_id' => $subfamilyId,
            'name' => $name,
            'sku' => trim($_POST['sku'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cost' => (float)($_POST['cost'] ?? 0),
            'stock' => (int)($_POST['stock'] ?? 0),
            'stock_min' => (int)($_POST['stock_min'] ?? 0),
            'status' => $_POST['status'] ?? 'activo',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'products', $id);
        flash('success', 'Producto actualizado correctamente.');
        $this->redirect('index.php?route=products');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $product = $this->products->findForCompany($id, $companyId);
        if (!$product) {
            flash('error', 'Producto no encontrado.');
            $this->redirect('index.php?route=products');
        }
        $this->products->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'products', $id);
        flash('success', 'Producto eliminado correctamente.');
        $this->redirect('index.php?route=products');
    }
}
