<?php

class ProducedProductsController extends Controller
{
    private ProducedProductsModel $products;
    private ProductsModel $regularProducts;
    private ProducedProductMaterialsModel $materials;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->products = new ProducedProductsModel($db);
        $this->regularProducts = new ProductsModel($db);
        $this->materials = new ProducedProductMaterialsModel($db);
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

        $this->render('produced-products/index', [
            'title' => 'Productos fabricados',
            'pageTitle' => 'Productos fabricados',
            'products' => $products,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $products = $this->regularProducts->active($companyId);

        $this->render('produced-products/create', [
            'title' => 'Nuevo producto fabricado',
            'pageTitle' => 'Nuevo producto fabricado',
            'products' => $products,
            'materials' => [],
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('index.php?route=produced-products/create');
        }

        $materials = $this->collectMaterials();
        $materialsCost = array_sum(array_column($materials, 'subtotal'));
        $cost = $materials ? $materialsCost : (float)($_POST['cost'] ?? 0);

        $productId = $this->products->create([
            'company_id' => $companyId,
            'name' => $name,
            'sku' => trim($_POST['sku'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cost' => $cost,
            'stock' => (int)($_POST['stock'] ?? 0),
            'stock_min' => (int)($_POST['stock_min'] ?? 0),
            'status' => $_POST['status'] ?? 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->materials->replaceForProduct($productId, $materials);

        audit($this->db, Auth::user()['id'], 'create', 'produced_products');
        flash('success', 'Producto fabricado creado correctamente.');
        $this->redirect('index.php?route=produced-products');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->products->findForCompany($id, $companyId);
        if (!$product) {
            $this->redirect('index.php?route=produced-products');
        }
        $products = $this->regularProducts->active($companyId);
        $materials = $this->materials->byProducedProduct($id);

        $this->render('produced-products/edit', [
            'title' => 'Editar producto fabricado',
            'pageTitle' => 'Editar producto fabricado',
            'product' => $product,
            'products' => $products,
            'materials' => $materials,
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
            $this->redirect('index.php?route=produced-products');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('index.php?route=produced-products/edit&id=' . $id);
        }

        $materials = $this->collectMaterials();
        $materialsCost = array_sum(array_column($materials, 'subtotal'));
        $cost = $materials ? $materialsCost : (float)($_POST['cost'] ?? 0);

        $this->products->update($id, [
            'name' => $name,
            'sku' => trim($_POST['sku'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cost' => $cost,
            'stock' => (int)($_POST['stock'] ?? 0),
            'stock_min' => (int)($_POST['stock_min'] ?? 0),
            'status' => $_POST['status'] ?? 'activo',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->materials->replaceForProduct($id, $materials);

        audit($this->db, Auth::user()['id'], 'update', 'produced_products', $id);
        flash('success', 'Producto fabricado actualizado correctamente.');
        $this->redirect('index.php?route=produced-products');
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
            $this->redirect('index.php?route=produced-products');
        }
        $this->products->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'produced_products', $id);
        flash('success', 'Producto fabricado eliminado correctamente.');
        $this->redirect('index.php?route=produced-products');
    }

    private function collectMaterials(): array
    {
        $productIds = $_POST['input_product_id'] ?? [];
        $quantities = $_POST['input_quantity'] ?? [];
        $unitCosts = $_POST['input_unit_cost'] ?? [];
        $materials = [];

        foreach ($productIds as $index => $productId) {
            $productId = (int)$productId;
            if ($productId <= 0) {
                continue;
            }
            $qty = max(0, (float)($quantities[$index] ?? 0));
            $unit = max(0, (float)($unitCosts[$index] ?? 0));
            $subtotal = $qty * $unit;
            if ($qty <= 0) {
                continue;
            }
            $materials[] = [
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_cost' => $unit,
                'subtotal' => $subtotal,
            ];
        }

        return $materials;
    }
}
