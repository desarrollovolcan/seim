<?php

class PurchaseOrdersController extends Controller
{
    private PurchaseOrdersModel $orders;
    private PurchaseOrderItemsModel $items;
    private SuppliersModel $suppliers;
    private ProductsModel $products;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->orders = new PurchaseOrdersModel($db);
        $this->items = new PurchaseOrderItemsModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->products = new ProductsModel($db);
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
        $orders = $this->orders->listWithRelations($companyId);

        $this->render('purchase-orders/index', [
            'title' => 'Órdenes de compra',
            'pageTitle' => 'Órdenes de compra',
            'orders' => $orders,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $suppliers = $this->suppliers->active($companyId);
        $products = $this->products->active($companyId);

        $this->render('purchase-orders/create', [
            'title' => 'Nueva orden de compra',
            'pageTitle' => 'Nueva orden de compra',
            'suppliers' => $suppliers,
            'products' => $products,
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $supplierId = (int)($_POST['supplier_id'] ?? 0);
        $supplier = $this->suppliers->findForCompany($supplierId, $companyId);
        if (!$supplier) {
            flash('error', 'Proveedor no válido.');
            $this->redirect('index.php?route=purchase-orders/create');
        }

        $items = $this->collectItems($companyId);
        if (empty($items)) {
            flash('error', 'Agrega al menos un producto a la orden de compra.');
            $this->redirect('index.php?route=purchase-orders/create');
        }

        $subtotal = array_sum(array_map(static fn(array $item) => $item['subtotal'], $items));
        $total = $subtotal;

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $orderId = $this->orders->create([
                'company_id' => $companyId,
                'supplier_id' => $supplierId,
                'reference' => trim($_POST['reference'] ?? ''),
                'order_date' => trim($_POST['order_date'] ?? date('Y-m-d')),
                'status' => $_POST['status'] ?? 'pendiente',
                'subtotal' => $subtotal,
                'total' => $total,
                'notes' => trim($_POST['notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($items as $item) {
                $this->items->create([
                    'purchase_order_id' => $orderId,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            audit($this->db, Auth::user()['id'], 'create', 'purchase_orders', $orderId);
            $pdo->commit();
            flash('success', 'Orden de compra registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error al registrar orden de compra: ' . $e->getMessage());
            flash('error', 'No pudimos guardar la orden de compra. Inténtalo nuevamente.');
        }

        $this->redirect('index.php?route=purchase-orders');
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orders->findForCompany($id, $companyId);
        if (!$order) {
            $this->redirect('index.php?route=purchase-orders');
        }

        $items = $this->db->fetchAll(
            'SELECT poi.*, p.name AS product_name, p.sku
             FROM purchase_order_items poi
             LEFT JOIN products p ON poi.product_id = p.id
             WHERE poi.purchase_order_id = :order_id
             ORDER BY poi.id ASC',
            ['order_id' => $id]
        );

        $this->render('purchase-orders/show', [
            'title' => 'Detalle orden de compra',
            'pageTitle' => 'Detalle orden de compra',
            'order' => $order,
            'items' => $items,
        ]);
    }

    private function collectItems(int $companyId): array
    {
        $productIds = $_POST['product_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitCosts = $_POST['unit_cost'] ?? [];
        $items = [];

        foreach ($productIds as $index => $productId) {
            $productId = (int)$productId;
            $quantity = max(0, (int)($quantities[$index] ?? 0));
            $unitCost = max(0.0, (float)($unitCosts[$index] ?? 0));
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $product = $this->products->findForCompany($productId, $companyId);
            if (!$product) {
                continue;
            }
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'subtotal' => $quantity * $unitCost,
            ];
        }

        return $items;
    }
}
