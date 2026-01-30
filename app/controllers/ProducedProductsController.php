<?php

class ProducedProductsController extends Controller
{
    private ProducedProductsModel $products;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->products = new ProducedProductsModel($db);
    }

    private function ensureTable(): void
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table',
            ['table' => 'produced_products']
        );
        if ((int)($row['total'] ?? 0) === 0) {
            flash('error', 'Faltan tablas de productos fabricados. Ejecuta la actualización de base de datos.');
            $this->redirect('index.php?route=dashboard');
        }
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
        $this->ensureTable();
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
        $this->ensureTable();
        $this->requireCompany();

        $this->render('produced-products/create', [
            'title' => 'Nuevo producto fabricado',
            'pageTitle' => 'Nuevo producto fabricado',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $this->ensureTable();
        $companyId = $this->requireCompany();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('index.php?route=produced-products/create');
        }

        $this->products->create([
            'company_id' => $companyId,
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

        audit($this->db, Auth::user()['id'], 'create', 'produced_products');
        flash('success', 'Producto fabricado creado correctamente.');
        $this->redirect('index.php?route=produced-products');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->ensureTable();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->products->findForCompany($id, $companyId);
        if (!$product) {
            $this->redirect('index.php?route=produced-products');
        }

        $this->render('produced-products/edit', [
            'title' => 'Editar producto fabricado',
            'pageTitle' => 'Editar producto fabricado',
            'product' => $product,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $this->ensureTable();
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

        $this->products->update($id, [
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

        audit($this->db, Auth::user()['id'], 'update', 'produced_products', $id);
        flash('success', 'Producto fabricado actualizado correctamente.');
        $this->redirect('index.php?route=produced-products');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $this->ensureTable();
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
}
