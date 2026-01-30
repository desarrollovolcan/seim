<?php

class PurchasesController extends Controller
{
    private PurchasesModel $purchases;
    private SuppliersModel $suppliers;
    private ProductsModel $products;
    private PurchaseItemsModel $purchaseItems;
    private SettingsModel $settings;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->purchases = new PurchasesModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->products = new ProductsModel($db);
        $this->purchaseItems = new PurchaseItemsModel($db);
        $this->settings = new SettingsModel($db);
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
        $purchases = $this->purchases->listWithRelations($companyId);

        $this->render('purchases/index', [
            'title' => 'Compras',
            'pageTitle' => 'Compras de productos',
            'purchases' => $purchases,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $suppliers = $this->suppliers->active($companyId);
        $products = $this->products->active($companyId);
        $invoiceDefaults = $this->settings->get('invoice_defaults', []);
        $taxDefault = !empty($invoiceDefaults['apply_tax']) ? (float)($invoiceDefaults['tax_rate'] ?? 0) : 0;

        $this->render('purchases/create', [
            'title' => 'Registrar compra',
            'pageTitle' => 'Registrar compra',
            'suppliers' => $suppliers,
            'products' => $products,
            'today' => date('Y-m-d'),
            'taxDefault' => $taxDefault,
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
            $this->redirect('index.php?route=purchases/create');
        }
        $siiData = sii_document_payload($_POST, sii_receiver_payload($supplier));
        $siiErrors = validate_sii_document_payload($siiData);
        if ($siiErrors) {
            flash('error', implode(' ', $siiErrors));
            $this->redirect('index.php?route=purchases/create');
        }

        $items = $this->collectItems($companyId);
        if (empty($items)) {
            flash('error', 'Agrega al menos un producto a la compra.');
            $this->redirect('index.php?route=purchases/create');
        }

        $subtotal = array_sum(array_map(static fn(array $item) => $item['subtotal'], $items));
        $tax = max(0, (float)($_POST['tax'] ?? 0));
        $total = $subtotal + $tax;

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $purchaseId = $this->purchases->create(array_merge([
                'company_id' => $companyId,
                'supplier_id' => $supplierId,
                'reference' => trim($_POST['reference'] ?? ''),
                'purchase_date' => trim($_POST['purchase_date'] ?? date('Y-m-d')),
                'status' => $_POST['status'] ?? 'pendiente',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => trim($_POST['notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], $siiData));

            foreach ($items as $item) {
                $this->purchaseItems->create([
                    'purchase_id' => $purchaseId,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->products->adjustStock($item['product']['id'], $item['quantity']);
            }

            audit($this->db, Auth::user()['id'], 'create', 'purchases', $purchaseId);
            $pdo->commit();
            flash('success', 'Compra registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error al registrar compra: ' . $e->getMessage());
            flash('error', 'No pudimos guardar la compra. Inténtalo nuevamente.');
        }

        $this->redirect('index.php?route=purchases');
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $purchase = $this->purchases->findForCompany($id, $companyId);
        if (!$purchase) {
            $this->redirect('index.php?route=purchases');
        }

        $items = $this->db->fetchAll(
            'SELECT pi.*, p.name AS product_name, p.sku
             FROM purchase_items pi
             LEFT JOIN products p ON pi.product_id = p.id
             WHERE pi.purchase_id = :purchase_id
             ORDER BY pi.id ASC',
            ['purchase_id' => $id]
        );

        $this->render('purchases/show', [
            'title' => 'Detalle compra',
            'pageTitle' => 'Detalle de compra',
            'purchase' => $purchase,
            'items' => $items,
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $purchase = $this->purchases->findForCompany($id, $companyId);
        if (!$purchase) {
            flash('error', 'Compra no encontrada.');
            $this->redirect('index.php?route=purchases');
        }
        $this->purchases->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'purchases', $id);
        flash('success', 'Compra eliminada correctamente.');
        $this->redirect('index.php?route=purchases');
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
