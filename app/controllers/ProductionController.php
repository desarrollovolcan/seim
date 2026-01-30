<?php

class ProductionController extends Controller
{
    private ProductionOrdersModel $orders;
    private ProductionInputsModel $inputs;
    private ProductionOutputsModel $outputs;
    private ProductionExpensesModel $expenses;
    private ProductsModel $products;
    private ProducedProductsModel $producedProducts;
    private ProducedProductMaterialsModel $materials;
    private InventoryMovementsModel $movements;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->orders = new ProductionOrdersModel($db);
        $this->inputs = new ProductionInputsModel($db);
        $this->outputs = new ProductionOutputsModel($db);
        $this->expenses = new ProductionExpensesModel($db);
        $this->products = new ProductsModel($db);
        $this->producedProducts = new ProducedProductsModel($db);
        $this->materials = new ProducedProductMaterialsModel($db);
        $this->movements = new InventoryMovementsModel($db);
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
        $orders = $this->orders->listWithTotals($companyId);

        $this->render('production/index', [
            'title' => 'Producción',
            'pageTitle' => 'Producción',
            'orders' => $orders,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $products = $this->products->active($companyId);
        $producedProducts = $this->producedProducts->active($companyId);
        $materials = $this->materials->byCompany($companyId);
        $materialsByProduct = [];
        foreach ($materials as $material) {
            $producedProductId = (int)($material['produced_product_id'] ?? 0);
            if (!$producedProductId) {
                continue;
            }
            $materialsByProduct[$producedProductId][] = [
                'product_id' => (int)$material['product_id'],
                'product_name' => $material['product_name'] ?? '',
                'quantity' => (float)$material['quantity'],
                'unit_cost' => (float)$material['unit_cost'],
                'product_cost' => (float)($material['product_cost'] ?? 0),
                'subtotal' => (float)$material['subtotal'],
            ];
        }

        $this->render('production/create', [
            'title' => 'Registrar producción',
            'pageTitle' => 'Registrar producción',
            'products' => $products,
            'producedProducts' => $producedProducts,
            'materialsByProduct' => $materialsByProduct,
            'today' => date('Y-m-d'),
        ]);
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orders->findForCompany($id, $companyId);
        if (!$order) {
            $this->redirect('index.php?route=production');
        }

        $outputs = $this->db->fetchAll(
            'SELECT po.*, pp.name AS product_name
             FROM production_outputs po
             JOIN produced_products pp ON po.produced_product_id = pp.id
             WHERE po.production_id = :production_id
             ORDER BY po.id ASC',
            ['production_id' => $id]
        );
        $inputs = $this->db->fetchAll(
            'SELECT pi.*, p.name AS product_name
             FROM production_inputs pi
             JOIN products p ON pi.product_id = p.id
             WHERE pi.production_id = :production_id
             ORDER BY pi.id ASC',
            ['production_id' => $id]
        );
        $expenses = $this->db->fetchAll(
            'SELECT * FROM production_expenses WHERE production_id = :production_id ORDER BY id ASC',
            ['production_id' => $id]
        );

        $this->render('production/show', [
            'title' => 'Detalle producción',
            'pageTitle' => 'Detalle de producción',
            'order' => $order,
            'outputs' => $outputs,
            'inputs' => $inputs,
            'expenses' => $expenses,
        ]);
    }

    public function stock(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $rows = $this->db->fetchAll(
            'SELECT p.id, p.name, p.sku, p.stock, p.cost, COALESCE(SUM(po.quantity), 0) AS produced_quantity
             FROM production_outputs po
             JOIN production_orders o ON o.id = po.production_id
             JOIN produced_products p ON p.id = po.produced_product_id
             WHERE o.company_id = :company_id
             GROUP BY p.id, p.name, p.sku, p.stock, p.cost
             ORDER BY p.name ASC',
            ['company_id' => $companyId]
        );

        $this->render('production/stock', [
            'title' => 'Stock producido',
            'pageTitle' => 'Stock de productos producidos',
            'rows' => $rows,
        ]);
    }

    public function inputsReport(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $rows = $this->db->fetchAll(
            'SELECT pi.*, p.name AS product_name, o.production_date
             FROM production_inputs pi
             JOIN production_orders o ON o.id = pi.production_id
             JOIN products p ON p.id = pi.product_id
             WHERE o.company_id = :company_id
             ORDER BY o.production_date DESC, pi.id DESC',
            ['company_id' => $companyId]
        );

        $this->render('production/inputs', [
            'title' => 'Consumos de producción',
            'pageTitle' => 'Consumos de producción',
            'rows' => $rows,
        ]);
    }

    public function expensesReport(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $rows = $this->db->fetchAll(
            'SELECT pe.*, o.production_date
             FROM production_expenses pe
             JOIN production_orders o ON o.id = pe.production_id
             WHERE o.company_id = :company_id
             ORDER BY o.production_date DESC, pe.id DESC',
            ['company_id' => $companyId]
        );

        $this->render('production/expenses', [
            'title' => 'Gastos de producción',
            'pageTitle' => 'Gastos de producción',
            'rows' => $rows,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $productionDate = trim($_POST['production_date'] ?? date('Y-m-d'));

        $outputs = $this->collectOutputs($companyId);
        if (empty($outputs)) {
            flash('error', 'Agrega al menos un producto final.');
            $this->redirect('index.php?route=production/create');
        }

        $computed = $this->buildInputsFromMaterials($companyId, $outputs);
        $inputs = $computed['inputs'];
        $outputMaterialCosts = $computed['output_material_costs'];
        if (empty($inputs)) {
            $inputs = $this->collectInputs($companyId);
            $outputMaterialCosts = [];
        }
        $expenses = $this->collectExpenses();

        $materialCost = array_sum(array_map(static fn(array $item) => $item['subtotal'], $inputs));
        $expensesTotal = array_sum(array_map(static fn(array $item) => $item['amount'], $expenses));
        $totalCost = $materialCost + $expensesTotal;

        if ($totalCost <= 0) {
            flash('error', 'Registra insumos o gastos para calcular el costo de producción.');
            $this->redirect('index.php?route=production/create');
        }

        $insufficient = [];
        foreach ($inputs as $input) {
            if (($input['product']['stock'] ?? 0) < $input['quantity']) {
                $insufficient[] = $input['product']['name'] ?? 'Producto';
            }
        }
        if ($insufficient) {
            flash('error', 'Stock insuficiente para: ' . implode(', ', $insufficient));
            $this->redirect('index.php?route=production/create');
        }

        $totalOutputQty = array_sum(array_map(static fn(array $item) => $item['quantity'], $outputs));
        $outputUnitCosts = [];
        foreach ($outputs as $output) {
            $productId = (int)($output['product']['id'] ?? 0);
            $quantity = (float)$output['quantity'];
            if ($quantity <= 0) {
                $outputUnitCosts[$productId] = 0;
                continue;
            }
            $materialCostForOutput = $outputMaterialCosts[$productId] ?? 0.0;
            if (empty($outputMaterialCosts) && $totalOutputQty > 0) {
                $materialCostForOutput = $materialCost * ($quantity / $totalOutputQty);
            }
            $expenseShare = 0.0;
            if ($expensesTotal > 0) {
                if ($materialCost > 0) {
                    $expenseShare = ($materialCostForOutput / $materialCost) * $expensesTotal;
                } elseif ($totalOutputQty > 0) {
                    $expenseShare = ($quantity / $totalOutputQty) * $expensesTotal;
                }
            }
            $outputUnitCosts[$productId] = ($materialCostForOutput + $expenseShare) / $quantity;
        }

        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $orderId = $this->orders->create([
                'company_id' => $companyId,
                'production_date' => $productionDate,
                'status' => 'completada',
                'total_cost' => $totalCost,
                'notes' => trim($_POST['notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($inputs as $input) {
                $this->inputs->create([
                    'production_id' => $orderId,
                    'product_id' => $input['product']['id'],
                    'quantity' => $input['quantity'],
                    'unit_cost' => $input['unit_cost'],
                    'subtotal' => $input['subtotal'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->products->adjustStock($input['product']['id'], -$input['quantity']);
                $this->movements->create([
                    'company_id' => $companyId,
                    'product_id' => $input['product']['id'],
                    'movement_date' => $productionDate,
                    'movement_type' => 'salida',
                    'quantity' => $input['quantity'],
                    'unit_cost' => $input['unit_cost'],
                    'reference_type' => 'production',
                    'reference_id' => $orderId,
                    'notes' => 'Consumo producción',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($outputs as $output) {
                $productId = (int)$output['product']['id'];
                $unitCost = $outputUnitCosts[$productId] ?? ($totalOutputQty > 0 ? $totalCost / $totalOutputQty : 0);
                $subtotal = $output['quantity'] * $unitCost;
                $this->outputs->create([
                    'production_id' => $orderId,
                    'produced_product_id' => $productId,
                    'quantity' => $output['quantity'],
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->producedProducts->adjustStock($productId, $output['quantity']);
                $this->producedProducts->updateCost($productId, $unitCost);
                $this->movements->create([
                    'company_id' => $companyId,
                    'produced_product_id' => $output['product']['id'],
                    'movement_date' => $productionDate,
                    'movement_type' => 'entrada',
                    'quantity' => $output['quantity'],
                    'unit_cost' => $unitCost,
                    'reference_type' => 'production',
                    'reference_id' => $orderId,
                    'notes' => 'Ingreso producción',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($expenses as $expense) {
                $this->expenses->create([
                    'production_id' => $orderId,
                    'description' => $expense['description'],
                    'amount' => $expense['amount'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            audit($this->db, Auth::user()['id'], 'create', 'production_orders', $orderId);
            $pdo->commit();
            flash('success', 'Producción registrada correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Error al registrar producción: ' . $e->getMessage());
            flash('error', 'No pudimos guardar la producción. Inténtalo nuevamente.');
        }

        $this->redirect('index.php?route=production');
    }

    private function collectOutputs(int $companyId): array
    {
        $productIds = $_POST['output_product_id'] ?? [];
        $quantities = $_POST['output_quantity'] ?? [];
        $outputs = [];

        foreach ($productIds as $index => $productId) {
            $productId = (int)$productId;
            $quantity = max(0, (int)($quantities[$index] ?? 0));
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $product = $this->producedProducts->findForCompany($productId, $companyId);
            if (!$product) {
                continue;
            }
            $outputs[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        return $outputs;
    }

    private function collectInputs(int $companyId): array
    {
        $productIds = $_POST['input_product_id'] ?? [];
        $quantities = $_POST['input_quantity'] ?? [];
        $unitCosts = $_POST['input_unit_cost'] ?? [];
        $inputs = [];

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
            $inputs[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'subtotal' => $quantity * $unitCost,
            ];
        }

        return $inputs;
    }

    private function buildInputsFromMaterials(int $companyId, array $outputs): array
    {
        $inputsByProduct = [];
        $outputMaterialCosts = [];
        foreach ($outputs as $output) {
            $producedProductId = (int)($output['product']['id'] ?? 0);
            $outputQty = (float)($output['quantity'] ?? 0);
            if ($producedProductId <= 0 || $outputQty <= 0) {
                continue;
            }
            $materials = $this->materials->byProducedProduct($producedProductId);
            if (!$materials) {
                continue;
            }
            foreach ($materials as $material) {
                $productId = (int)($material['product_id'] ?? 0);
                if ($productId <= 0) {
                    continue;
                }
                $quantity = (float)($material['quantity'] ?? 0) * $outputQty;
                if ($quantity <= 0) {
                    continue;
                }
                if (!isset($inputsByProduct[$productId])) {
                    $product = $this->products->findForCompany($productId, $companyId);
                    if (!$product) {
                        continue;
                    }
                    $inputsByProduct[$productId] = [
                        'product' => $product,
                        'quantity' => 0,
                        'unit_cost' => 0.0,
                        'subtotal' => 0,
                    ];
                }
                $unitCost = (float)($material['unit_cost'] ?? 0);
                if ($unitCost <= 0) {
                    $unitCost = (float)($inputsByProduct[$productId]['product']['cost'] ?? 0);
                }
                if ($inputsByProduct[$productId]['unit_cost'] <= 0 && $unitCost > 0) {
                    $inputsByProduct[$productId]['unit_cost'] = $unitCost;
                }
                $subtotal = $quantity * $unitCost;
                $inputsByProduct[$productId]['quantity'] += $quantity;
                $inputsByProduct[$productId]['subtotal'] += $subtotal;
                $outputMaterialCosts[$producedProductId] = ($outputMaterialCosts[$producedProductId] ?? 0) + $subtotal;
            }
        }

        return [
            'inputs' => array_values($inputsByProduct),
            'output_material_costs' => $outputMaterialCosts,
        ];
    }

    private function collectExpenses(): array
    {
        $descriptions = $_POST['expense_description'] ?? [];
        $amounts = $_POST['expense_amount'] ?? [];
        $expenses = [];

        foreach ($descriptions as $index => $description) {
            $description = trim((string)$description);
            $amount = max(0.0, (float)($amounts[$index] ?? 0));
            if ($description === '' || $amount <= 0) {
                continue;
            }
            $expenses[] = [
                'description' => $description,
                'amount' => $amount,
            ];
        }

        return $expenses;
    }
}
