<?php

class SalesController extends Controller
{
    private SalesModel $sales;
    private SaleItemsModel $saleItems;
    private ProductsModel $products;
    private ClientsModel $clients;
    private PosSessionsModel $posSessions;
    private SalePaymentsModel $salePayments;
    private ServicesModel $services;
    private SettingsModel $settings;
    private ProducedProductsModel $producedProducts;
    private PosSessionWithdrawalsModel $posWithdrawals;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->sales = new SalesModel($db);
        $this->saleItems = new SaleItemsModel($db);
        $this->products = new ProductsModel($db);
        $this->clients = new ClientsModel($db);
        $this->posSessions = new PosSessionsModel($db);
        $this->salePayments = new SalePaymentsModel($db);
        $this->services = new ServicesModel($db);
        $this->settings = new SettingsModel($db);
        $this->producedProducts = new ProducedProductsModel($db);
        $this->posWithdrawals = new PosSessionWithdrawalsModel($db);
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
        $sales = $this->sales->listWithRelations($companyId);

        $this->render('sales/index', [
            'title' => 'Ventas',
            'pageTitle' => 'Ventas de productos',
            'sales' => $sales,
        ]);
    }

    public function profitAnalysis(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $products = $this->products->active($companyId);
        $analysis = [];

        foreach ($products as $product) {
            $salePrice = (float)($product['price'] ?? 0);
            $supplierPrice = (float)($product['supplier_price'] ?? 0);
            $competitionPrice = (float)($product['competition_price'] ?? 0);
            $profitSupplier = $salePrice - $supplierPrice;
            $profitCompetition = $salePrice - $competitionPrice;

            $analysis[] = [
                'product' => $product,
                'sale_price' => $salePrice,
                'supplier_price' => $supplierPrice,
                'competition_price' => $competitionPrice,
                'profit_supplier' => $profitSupplier,
                'profit_competition' => $profitCompetition,
                'profit_supplier_pct' => $supplierPrice > 0 ? ($profitSupplier / $supplierPrice) * 100 : null,
                'profit_competition_pct' => $competitionPrice > 0 ? ($profitCompetition / $competitionPrice) * 100 : null,
            ];
        }

        $this->render('sales/profit-analysis', [
            'title' => 'Análisis de ganancias',
            'pageTitle' => 'Análisis de ganancias',
            'analysis' => $analysis,
        ]);
    }

    public function create(): void
    {
        $this->renderForm(false);
    }

    public function pos(): void
    {
        $this->renderForm(true);
    }

    private function renderForm(bool $isPos): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $products = $this->products->active($companyId);
        $producedProducts = $this->producedProducts->active($companyId);
        $clients = $this->clients->active($companyId);
        $services = $this->services->active($companyId);
        $invoiceDefaults = $this->settings->get('invoice_defaults', []);
        $applyTaxDefault = $isPos ? true : !empty($invoiceDefaults['apply_tax']);
        $taxRate = (float)($invoiceDefaults['tax_rate'] ?? 19);
        $taxDefault = $applyTaxDefault ? $taxRate : 0;
        $session = null;
        $sessionTotals = [];
        $sessionWithdrawals = 0.0;
        $posReady = $this->posTablesReady();
        $recentSessionSales = [];
        $posSessionsSummary = [];
        $posSummaryTotals = [
            'opening' => 0.0,
            'sales' => 0.0,
            'withdrawals' => 0.0,
            'closing' => 0.0,
        ];
        $printSaleId = 0;
        if ($isPos) {
            if ($posReady) {
                $session = $this->posSessions->activeForUser($companyId, (int)(Auth::user()['id'] ?? 0));
                if ($session) {
                    $sessionTotals = $this->salePayments->totalsBySession((int)$session['id']);
                    $recentSessionSales = $this->sales->recentBySession((int)$session['id'], $companyId);
                    $sessionWithdrawals = $this->posWithdrawals->totalBySession((int)$session['id']);
                }
                $posSessionsSummary = $this->posSessions->listSummary($companyId, 12);
                foreach ($posSessionsSummary as $summary) {
                    $posSummaryTotals['opening'] += (float)($summary['opening_amount'] ?? 0);
                    $posSummaryTotals['sales'] += (float)($summary['sales_total'] ?? 0);
                    $posSummaryTotals['withdrawals'] += (float)($summary['withdrawals_total'] ?? 0);
                    $posSummaryTotals['closing'] += (float)($summary['closing_amount'] ?? 0);
                }
            } else {
                flash('error', 'Faltan tablas/columnas para el POS. Ejecuta la actualización de base de datos.');
            }
            $printSaleId = (int)($_GET['print_sale_id'] ?? 0);
        }

        $this->render('sales/create', [
            'title' => $isPos ? 'Punto de venta' : 'Registrar venta',
            'pageTitle' => $isPos ? 'Punto de venta' : 'Registrar venta',
            'products' => $products,
            'producedProducts' => $producedProducts,
            'clients' => $clients,
            'services' => $services,
            'today' => date('Y-m-d'),
            'taxDefault' => $taxDefault,
            'taxRate' => $taxRate,
            'applyTaxDefault' => $applyTaxDefault,
            'isPos' => $isPos,
            'posSession' => $session,
            'sessionTotals' => $sessionTotals,
            'sessionWithdrawals' => $sessionWithdrawals,
            'posReady' => $posReady,
            'recentSessionSales' => $recentSessionSales,
            'posSessionsSummary' => $posSessionsSummary,
            'posSummaryTotals' => $posSummaryTotals,
            'printSaleId' => $printSaleId,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $userId = (int)(Auth::user()['id'] ?? 0);
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = null;
        $quickSale = !empty($_POST['quick_sale']);
        if ($quickSale) {
            $clientId = 0;
        }
        if ($clientId > 0) {
            $client = $this->db->fetch(
            'SELECT id, rut, name, giro, address, commune FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
            if (!$client) {
                flash('error', 'Cliente no válido para esta empresa.');
                $this->redirect('index.php?route=sales/create');
            }
        }

        $isPos = ($_POST['channel'] ?? '') === 'pos';
        $siiData = sii_document_payload($_POST, $client ? sii_receiver_payload($client) : []);
        if (!$isPos || !$quickSale || $clientId > 0) {
            $siiErrors = validate_sii_document_payload($siiData);
            if ($siiErrors) {
                flash('error', implode(' ', $siiErrors));
                $this->redirect($isPos ? 'index.php?route=pos' : 'index.php?route=sales/create');
            }
        }
        $posSessionId = null;
        if ($isPos) {
            if (!$this->posTablesReady()) {
                flash('error', 'El POS no está disponible hasta aplicar las migraciones de BD.');
                $this->redirect('index.php?route=pos');
            }
            $session = $this->posSessions->activeForUser($companyId, $userId);
            if (!$session) {
                flash('error', 'Debes abrir una caja de POS antes de registrar ventas.');
                $this->redirect('index.php?route=pos');
            }
            $posSessionId = (int)$session['id'];
        }
        $items = $this->collectItems($companyId, $isPos);
        if (empty($items)) {
            flash('error', 'Agrega al menos un producto a la venta.');
            $this->redirect($isPos ? 'index.php?route=pos' : 'index.php?route=sales/create');
        }

        $prefix = $isPos ? 'POS-' : 'VEN-';
        $numero = $this->sales->nextNumber($prefix, $companyId);
        $subtotal = array_sum(array_map(static fn(array $item) => $item['subtotal'], $items));
        $applyTax = !empty($_POST['apply_tax']);
        $taxRate = (float)($_POST['tax_rate'] ?? 0);
        $tax = $applyTax ? max(0, round($subtotal * $taxRate / 100, 2)) : 0.0;
        $total = $subtotal + $tax;
        $status = $_POST['status'] ?? ($isPos ? 'pagado' : 'pendiente');
        $allowedStatus = ['pagado', 'pendiente', 'borrador', 'en_espera'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 'pagado';
        }

        $pdo = $this->db->pdo();
        $saleId = null;
        try {
            $pdo->beginTransaction();
            $saleId = $this->sales->create(array_merge([
                'company_id' => $companyId,
                'client_id' => $clientId ?: null,
                'pos_session_id' => $posSessionId,
                'channel' => $isPos ? 'pos' : 'venta',
                'numero' => $numero,
                'sale_date' => trim($_POST['sale_date'] ?? date('Y-m-d')),
                'status' => $status,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => trim($_POST['notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], $siiData));

            foreach ($items as $item) {
                $this->saleItems->create([
                    'sale_id' => $saleId,
                    'product_id' => $item['product']['id'] ?? null,
                    'produced_product_id' => $item['produced_product']['id'] ?? null,
                    'service_id' => $item['service']['id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                if (!empty($item['product']['id'])) {
                    $this->products->adjustStock($item['product']['id'], -$item['quantity']);
                }
                if (!empty($item['produced_product']['id'])) {
                    $this->producedProducts->adjustStock($item['produced_product']['id'], -$item['quantity']);
                }
            }
            if ($this->salePaymentsEnabled()) {
                $paymentMethod = $_POST['payment_method'] ?? 'efectivo';
                $this->salePayments->create([
                    'sale_id' => $saleId,
                    'method' => $paymentMethod,
                    'amount' => $total,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            audit($this->db, Auth::user()['id'], 'create', 'sales', $saleId);
            $pdo->commit();
            flash('success', 'Venta registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error al registrar venta: ' . $e->getMessage());
            flash('error', 'No pudimos guardar la venta. Revisa los datos e intenta nuevamente.');
        }

        if ($isPos && $saleId) {
            $this->redirect('index.php?route=pos&print_sale_id=' . $saleId);
        }
        $this->redirect($isPos ? 'index.php?route=pos' : 'index.php?route=sales');
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $sale = $this->sales->findForCompany($id, $companyId);
        if (!$sale) {
            $this->redirect('index.php?route=sales');
        }

        $items = $this->db->fetchAll(
            'SELECT si.*,
                    COALESCE(p.name, pp.name) AS product_name,
                    COALESCE(p.sku, pp.sku) AS sku
             FROM sale_items si
             LEFT JOIN products p ON si.product_id = p.id
             LEFT JOIN produced_products pp ON si.produced_product_id = pp.id
             WHERE si.sale_id = :sale_id
             ORDER BY si.id ASC',
            ['sale_id' => $id]
        );

        $this->render('sales/show', [
            'title' => 'Detalle de venta',
            'pageTitle' => 'Detalle de venta',
            'sale' => $sale,
            'items' => $items,
        ]);
    }

    public function receipt(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $copies = max(1, (int)($_GET['copies'] ?? 1));
        $sale = $this->sales->findForCompany($id, $companyId);
        if (!$sale) {
            $this->redirect('index.php?route=sales');
        }
        $items = $this->db->fetchAll(
            'SELECT si.*,
                    COALESCE(p.name, pp.name) AS product_name,
                    COALESCE(p.sku, pp.sku) AS sku
             FROM sale_items si
             LEFT JOIN products p ON si.product_id = p.id
             LEFT JOIN produced_products pp ON si.produced_product_id = pp.id
             WHERE si.sale_id = :sale_id
             ORDER BY si.id ASC',
            ['sale_id' => $id]
        );
        $company = (new SettingsModel($this->db))->get('company', []);
        $viewPath = __DIR__ . '/../views/sales/receipt.php';
        if (file_exists($viewPath)) {
            include $viewPath;
            return;
        }
        echo 'Vista no encontrada.';
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $sale = $this->sales->findForCompany($id, $companyId);
        if (!$sale) {
            flash('error', 'Venta no encontrada.');
            $this->redirect('index.php?route=sales');
        }
        $this->sales->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'sales', $id);
        flash('success', 'Venta eliminada correctamente.');
        $this->redirect('index.php?route=sales');
    }

    private function collectItems(int $companyId, bool $isPos): array
    {
        $productIds = $_POST['product_id'] ?? [];
        $producedProductIds = $_POST['produced_product_id'] ?? [];
        $serviceIds = $_POST['service_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitPrices = $_POST['unit_price'] ?? [];
        $types = $_POST['item_type'] ?? [];
        $items = [];

        $max = max(count($productIds), count($producedProductIds), count($serviceIds), count($quantities));
        for ($index = 0; $index < $max; $index++) {
            $type = $types[$index] ?? 'product';
            $productId = (int)($productIds[$index] ?? 0);
            $producedProductId = (int)($producedProductIds[$index] ?? 0);
            $serviceId = (int)($serviceIds[$index] ?? 0);
            $quantity = max(0, (int)($quantities[$index] ?? 0));
            $unitPrice = max(0.0, (float)($unitPrices[$index] ?? 0));
            if ($quantity <= 0) {
                continue;
            }
            if ($type === 'service' && $serviceId > 0) {
                $service = $this->services->find($serviceId);
                if (!$service || (int)$service['company_id'] !== $companyId) {
                    continue;
                }
                $price = $unitPrice > 0 ? $unitPrice : (float)($service['cost'] ?? 0);
                $items[] = [
                    'product' => null,
                    'produced_product' => null,
                    'service' => $service,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $quantity * $price,
                ];
                continue;
            }
            if ($type === 'produced_product' && $producedProductId > 0) {
                $producedProduct = $this->producedProducts->findForCompany($producedProductId, $companyId);
                if (!$producedProduct) {
                    continue;
                }
                if ((int)$producedProduct['stock'] < $quantity) {
                    flash('error', sprintf('Stock insuficiente para %s. Disponible: %d', $producedProduct['name'], (int)$producedProduct['stock']));
                    $this->redirect($isPos ? 'index.php?route=pos' : 'index.php?route=sales/create');
                }
                $price = $unitPrice > 0 ? $unitPrice : (float)($producedProduct['price'] ?? 0);
                $items[] = [
                    'product' => null,
                    'produced_product' => $producedProduct,
                    'service' => null,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $quantity * $price,
                ];
                continue;
            }
            if ($productId > 0) {
                $product = $this->products->findForCompany($productId, $companyId);
                if (!$product) {
                    continue;
                }
                if ((int)$product['stock'] < $quantity) {
                    flash('error', sprintf('Stock insuficiente para %s. Disponible: %d', $product['name'], (int)$product['stock']));
                    $this->redirect($isPos ? 'index.php?route=pos' : 'index.php?route=sales/create');
                }
                $items[] = [
                    'product' => $product,
                    'produced_product' => null,
                    'service' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice > 0 ? $unitPrice : (float)($product['price'] ?? 0),
                    'subtotal' => $quantity * ($unitPrice > 0 ? $unitPrice : (float)($product['price'] ?? 0)),
                ];
            }
        }

        return $items;
    }

    public function openSession(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $userId = (int)(Auth::user()['id'] ?? 0);
        if (!$this->posTablesReady()) {
            flash('error', 'El POS no está disponible hasta aplicar las migraciones de BD.');
            $this->redirect('index.php?route=pos');
        }
        $amount = max(0, (float)($_POST['opening_amount'] ?? 0));
        $current = $this->posSessions->activeForUser($companyId, $userId);
        if ($current) {
            flash('error', 'Ya tienes una sesión abierta.');
            $this->redirect('index.php?route=pos');
        }
        $this->posSessions->openSession($companyId, $userId, $amount);
        flash('success', 'Caja abierta correctamente.');
        $this->redirect('index.php?route=pos');
    }

    public function closeSession(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $userId = (int)(Auth::user()['id'] ?? 0);
        if (!$this->posTablesReady()) {
            flash('error', 'El POS no está disponible hasta aplicar las migraciones de BD.');
            $this->redirect('index.php?route=pos');
        }
        $amount = max(0, (float)($_POST['closing_amount'] ?? 0));
        $session = $this->posSessions->activeForUser($companyId, $userId);
        if (!$session) {
            flash('error', 'No hay una sesión abierta.');
            $this->redirect('index.php?route=pos');
        }
        $this->posSessions->closeSession((int)$session['id'], $amount);
        flash('success', 'Caja cerrada correctamente.');
        $this->redirect('index.php?route=pos');
    }

    public function withdrawSession(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $userId = (int)(Auth::user()['id'] ?? 0);
        if (!$this->posTablesReady()) {
            flash('error', 'El POS no está disponible hasta aplicar las migraciones de BD.');
            $this->redirect('index.php?route=pos');
        }
        $amount = max(0, (float)($_POST['withdraw_amount'] ?? 0));
        $reason = trim((string)($_POST['withdraw_reason'] ?? ''));
        if ($amount <= 0) {
            flash('error', 'Indica un monto válido para el retiro.');
            $this->redirect('index.php?route=pos');
        }
        if ($reason === '') {
            flash('error', 'Indica el motivo del retiro.');
            $this->redirect('index.php?route=pos');
        }
        $session = $this->posSessions->activeForUser($companyId, $userId);
        if (!$session) {
            flash('error', 'No hay una sesión abierta.');
            $this->redirect('index.php?route=pos');
        }
        $this->posWithdrawals->create([
            'pos_session_id' => (int)$session['id'],
            'company_id' => $companyId,
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Retiro registrado correctamente.');
        $this->redirect('index.php?route=pos');
    }

    private function tableExists(string $table): bool
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table',
            ['table' => $table]
        );
        return (int)($row['total'] ?? 0) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
            ['table' => $table, 'column' => $column]
        );
        return (int)($row['total'] ?? 0) > 0;
    }

    private function posTablesReady(): bool
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }
        $ready = $this->tableExists('pos_sessions')
            && $this->tableExists('sale_payments')
            && $this->tableExists('pos_session_withdrawals')
            && $this->columnExists('sales', 'pos_session_id');
        return $ready;
    }

    private function salePaymentsEnabled(): bool
    {
        static $available = null;
        if ($available !== null) {
            return $available;
        }
        $available = $this->tableExists('sale_payments');
        return $available;
    }
}
