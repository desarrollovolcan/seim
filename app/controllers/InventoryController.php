<?php

class InventoryController extends Controller
{
    private InventoryMovementsModel $movements;
    private ProductsModel $products;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->movements = new InventoryMovementsModel($db);
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

    public function movements(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $products = $this->products->active($companyId);
        $movements = $this->movements->byCompany($companyId);
        $this->render('inventory/movements', [
            'title' => 'Movimientos de inventario',
            'pageTitle' => 'Movimientos de inventario',
            'products' => $products,
            'movements' => $movements,
            'today' => date('Y-m-d'),
        ]);
    }

    public function showMovement(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $movementId = (int)($_GET['id'] ?? 0);
        $movement = $this->db->fetch(
            'SELECT im.*, p.name as product_name
             FROM inventory_movements im
             JOIN products p ON im.product_id = p.id
             WHERE im.id = :id AND im.company_id = :company_id',
            ['id' => $movementId, 'company_id' => $companyId]
        );
        if (!$movement) {
            flash('error', 'Movimiento de inventario no encontrado.');
            $this->redirect('index.php?route=inventory/movements');
        }
        $this->render('inventory/movement-show', [
            'title' => 'Detalle movimiento de inventario',
            'pageTitle' => 'Detalle movimiento de inventario',
            'movement' => $movement,
        ]);
    }

    public function storeMovement(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $productId = (int)($_POST['product_id'] ?? 0);
        $movementType = $_POST['movement_type'] ?? 'entrada';
        $quantity = (int)($_POST['quantity'] ?? 0);
        $product = $this->products->findForCompany($productId, $companyId);
        if (!$product || $quantity <= 0) {
            flash('error', 'Selecciona un producto válido y cantidad.');
            $this->redirect('index.php?route=inventory/movements');
        }
        $this->movements->create([
            'company_id' => $companyId,
            'product_id' => $productId,
            'movement_date' => trim($_POST['movement_date'] ?? date('Y-m-d')),
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'unit_cost' => (float)($_POST['unit_cost'] ?? 0),
            'reference_type' => trim($_POST['reference_type'] ?? ''),
            'reference_id' => (int)($_POST['reference_id'] ?? 0) ?: null,
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $adjust = $movementType === 'salida' ? -$quantity : $quantity;
        $this->products->adjustStock($productId, $adjust);
        flash('success', 'Movimiento de inventario registrado.');
        $this->redirect('index.php?route=inventory/movements');
    }

    public function editMovement(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $movementId = (int)($_GET['id'] ?? 0);
        $movement = $this->db->fetch(
            'SELECT im.*, p.name as product_name
             FROM inventory_movements im
             JOIN products p ON im.product_id = p.id
             WHERE im.id = :id AND im.company_id = :company_id',
            ['id' => $movementId, 'company_id' => $companyId]
        );
        if (!$movement) {
            flash('error', 'Movimiento de inventario no encontrado.');
            $this->redirect('index.php?route=inventory/movements');
        }
        $this->render('inventory/movement-edit', [
            'title' => 'Editar movimiento de inventario',
            'pageTitle' => 'Editar movimiento de inventario',
            'movement' => $movement,
        ]);
    }

    public function updateMovement(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $movementId = (int)($_POST['id'] ?? 0);
        $movement = $this->db->fetch(
            'SELECT id FROM inventory_movements WHERE id = :id AND company_id = :company_id',
            ['id' => $movementId, 'company_id' => $companyId]
        );
        if (!$movement) {
            flash('error', 'Movimiento de inventario no encontrado.');
            $this->redirect('index.php?route=inventory/movements');
        }
        $this->movements->update($movementId, [
            'reference_type' => trim($_POST['reference_type'] ?? ''),
            'reference_id' => (int)($_POST['reference_id'] ?? 0) ?: null,
            'notes' => trim($_POST['notes'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Movimiento de inventario actualizado.');
        $this->redirect('index.php?route=inventory/movements');
    }
}
