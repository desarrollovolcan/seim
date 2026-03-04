<?php

class PurchasesController extends Controller
{
    private PurchasesModel $purchases;
    private SuppliersModel $suppliers;
    private ProductsModel $products;
    private PettyCashProductsModel $pettyCashProducts;
    private PurchaseItemsModel $purchaseItems;
    private SettingsModel $settings;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->purchases = new PurchasesModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->products = new ProductsModel($db);
        $this->pettyCashProducts = new PettyCashProductsModel($db);
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
            'pageTitle' => 'Compras y gastos',
            'purchases' => $purchases,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $suppliers = $this->suppliers->active($companyId);
        $catalogProducts = $this->pettyCashProducts->active($companyId);
        $invoiceDefaults = $this->settings->get('invoice_defaults', []);
        $taxDefault = !empty($invoiceDefaults['apply_tax']) ? (float)($invoiceDefaults['tax_rate'] ?? 0) : 0;

        $this->render('purchases/create', [
            'title' => 'Registrar compra',
            'pageTitle' => 'Registrar compra',
            'suppliers' => $suppliers,
            'catalogProducts' => $catalogProducts,
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

        $items = $this->collectItems($companyId);
        $hasPettyCashProductColumn = $this->purchaseItems->hasPettyCashProductColumn();
        $hasUnitMeasureColumn = $this->purchaseItems->hasUnitMeasureColumn();
        $hasItemTypeColumn = $this->purchaseItems->hasItemTypeColumn();

        if (empty($items)) {
            flash('error', 'Agrega al menos un ítem (producto o servicio) a la compra.');
            $this->redirect('index.php?route=purchases/create');
        }

        $subtotal = array_sum(array_map(static fn(array $item) => $item['subtotal'], $items));
        $tax = max(0, (float)($_POST['tax'] ?? 0));
        $total = $subtotal + $tax;

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $purchaseData = array_merge([
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
            ], $siiData);
            $purchaseData = $this->filterExistingPurchaseColumns($purchaseData);

            $purchaseId = $this->purchases->create($purchaseData);

            foreach ($items as $item) {
                $itemData = [
                    'purchase_id' => $purchaseId,
                    'description' => $item['description'],
                    'product_id' => null,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($hasItemTypeColumn) {
                    $itemData['item_type'] = $item['item_type'];
                }
                if ($hasPettyCashProductColumn) {
                    $itemData['petty_cash_product_id'] = $item['petty_cash_product']['id'] ?? null;
                }
                if ($hasUnitMeasureColumn) {
                    $itemData['unit_measure'] = $item['unit_measure'] ?? 'Unidad';
                }

                $this->purchaseItems->create($itemData);
            }

            audit($this->db, Auth::user()['id'], 'create', 'purchases', $purchaseId);
            $pdo->commit();
            flash('success', 'Compra registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error al registrar compra: ' . $e->getMessage() . ' | code: ' . (string)$e->getCode());
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
            'SELECT pi.*, p.name AS product_name, p.sku, pc.name AS petty_cash_product_name
             FROM purchase_items pi
             LEFT JOIN products p ON pi.product_id = p.id
             LEFT JOIN petty_cash_products pc ON pi.petty_cash_product_id = pc.id
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

    public function storeCatalogProduct(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['name'] ?? '');
        $classification = trim($_POST['classification'] ?? 'servicio');
        $category = trim($_POST['category'] ?? 'General');
        $unitMeasure = trim($_POST['unit_measure'] ?? 'Unidad');
        $suggestedPrice = max(0, (float)($_POST['suggested_price'] ?? 0));

        if (!in_array($classification, ['producto', 'servicio'], true)) {
            $classification = 'servicio';
        }

        if ($name === '') {
            flash('error', 'Debes ingresar el nombre del ítem.');
            $this->redirect('index.php?route=purchases/create');
        }

        try {
            $catalogData = [
                'company_id' => $companyId,
                'name' => $name,
                'classification' => $classification,
                'category' => $category,
                'suggested_price' => $suggestedPrice,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($this->pettyCashProducts->hasUnitMeasureColumn()) {
                $catalogData['unit_measure'] = $unitMeasure !== '' ? $unitMeasure : 'Unidad';
            }

            $this->pettyCashProducts->create($catalogData);
            flash('success', 'Ítem agregado al catálogo compartido correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Error al crear ítem para compras: ' . $e->getMessage());
            flash('error', 'No se pudo guardar el ítem en el catálogo.');
        }

        $this->redirect('index.php?route=purchases/create');
    }

    public function storeQuickSupplier(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['supplier_name'] ?? '');
        $code = trim($_POST['supplier_code'] ?? '');
        $email = trim($_POST['supplier_email'] ?? '');
        $website = trim($_POST['supplier_website'] ?? '');

        if (!Validator::required($name) || !Validator::required($code)) {
            flash('error', 'Nombre y código del proveedor son obligatorios.');
            $this->redirect('index.php?route=purchases/create');
        }
        if (!Validator::optionalEmail($email)) {
            flash('error', 'El email del proveedor no es válido.');
            $this->redirect('index.php?route=purchases/create');
        }
        if (!Validator::url($website)) {
            flash('error', 'El sitio web del proveedor no es válido.');
            $this->redirect('index.php?route=purchases/create');
        }

        try {
            $this->suppliers->create([
                'company_id' => $companyId,
                'name' => $name,
                'code' => $code,
                'contact_name' => trim($_POST['supplier_contact_name'] ?? ''),
                'tax_id' => trim($_POST['supplier_tax_id'] ?? ''),
                'email' => $email,
                'phone' => trim($_POST['supplier_phone'] ?? ''),
                'address' => trim($_POST['supplier_address'] ?? ''),
                'giro' => trim($_POST['supplier_giro'] ?? ''),
                'commune' => trim($_POST['supplier_commune'] ?? ''),
                'website' => $website,
                'notes' => trim($_POST['supplier_notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            flash('success', 'Proveedor creado correctamente. Ya puedes seleccionarlo en la compra.');
        } catch (Throwable $e) {
            log_message('error', 'Error creando proveedor rápido en compras: ' . $e->getMessage());
            flash('error', 'No se pudo crear el proveedor.');
        }

        $this->redirect('index.php?route=purchases/create');
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


    private function filterExistingPurchaseColumns(array $data): array
    {
        $filtered = [];
        foreach ($data as $column => $value) {
            if ($this->purchases->hasColumnNamed((string)$column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }

    private function collectItems(int $companyId): array
    {
        $catalogIds = $_POST['catalog_product_id'] ?? [];
        $descriptions = $_POST['description'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitCosts = $_POST['unit_cost'] ?? [];
        $unitMeasures = $_POST['unit_measure'] ?? [];
        $items = [];

        foreach ($quantities as $index => $rawQuantity) {
            $catalogId = (int)($catalogIds[$index] ?? 0);
            $description = trim((string)($descriptions[$index] ?? ''));
            $quantity = max(0, (int)$rawQuantity);
            $unitCost = max(0.0, (float)($unitCosts[$index] ?? 0));
            $unitMeasure = trim((string)($unitMeasures[$index] ?? 'Unidad'));

            if ($quantity <= 0) {
                continue;
            }

            $catalogProduct = null;
            $itemType = 'servicio';
            if ($catalogId > 0) {
                $catalogProduct = $this->pettyCashProducts->findForCompany($catalogId, $companyId);
                if (!$catalogProduct) {
                    continue;
                }
                $itemType = ($catalogProduct['classification'] ?? $catalogProduct['category'] ?? '') === 'producto' ? 'producto' : 'servicio';
                if ($description === '') {
                    $description = (string)($catalogProduct['name'] ?? 'Ítem');
                }
                if ($unitCost <= 0) {
                    $unitCost = (float)($catalogProduct['suggested_price'] ?? 0);
                }
                if ($unitMeasure === '') {
                    $unitMeasure = (string)($catalogProduct['unit_measure'] ?? 'Unidad');
                }
            }

            if ($description === '') {
                continue;
            }

            $items[] = [
                'item_type' => $itemType,
                'description' => $description,
                'petty_cash_product' => $catalogProduct,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'unit_measure' => $unitMeasure !== '' ? $unitMeasure : 'Unidad',
                'subtotal' => $quantity * $unitCost,
            ];
        }

        return $items;
    }
}
