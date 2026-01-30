<?php

class FixedAssetsController extends Controller
{
    private FixedAssetsModel $assets;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->assets = new FixedAssetsModel($db);
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
        $assets = $this->assets->byCompany($companyId);
        $this->render('fixed-assets/index', [
            'title' => 'Activos fijos',
            'pageTitle' => 'Activos fijos',
            'assets' => $assets,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireCompany();
        $this->render('fixed-assets/create', [
            'title' => 'Registrar activo fijo',
            'pageTitle' => 'Registrar activo fijo',
            'today' => date('Y-m-d'),
        ]);
    }

    public function show(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $assetId = (int)($_GET['id'] ?? 0);
        $asset = $this->db->fetch(
            'SELECT * FROM fixed_assets WHERE id = :id AND company_id = :company_id',
            ['id' => $assetId, 'company_id' => $companyId]
        );
        if (!$asset) {
            flash('error', 'Activo fijo no encontrado.');
            $this->redirect('index.php?route=fixed-assets');
        }
        $this->render('fixed-assets/show', [
            'title' => 'Detalle de activo fijo',
            'pageTitle' => 'Detalle de activo fijo',
            'asset' => $asset,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $value = (float)($_POST['acquisition_value'] ?? 0);
        $accumulated = (float)($_POST['accumulated_depreciation'] ?? 0);
        $bookValue = max(0, $value - $accumulated);
        $this->assets->create([
            'company_id' => $companyId,
            'name' => trim($_POST['name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'acquisition_date' => trim($_POST['acquisition_date'] ?? date('Y-m-d')),
            'acquisition_value' => $value,
            'depreciation_method' => $_POST['depreciation_method'] ?? 'linea_recta',
            'useful_life_months' => (int)($_POST['useful_life_months'] ?? 0),
            'accumulated_depreciation' => $accumulated,
            'book_value' => $bookValue,
            'status' => $_POST['status'] ?? 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Activo fijo registrado.');
        $this->redirect('index.php?route=fixed-assets');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $assetId = (int)($_GET['id'] ?? 0);
        $asset = $this->db->fetch(
            'SELECT * FROM fixed_assets WHERE id = :id AND company_id = :company_id',
            ['id' => $assetId, 'company_id' => $companyId]
        );
        if (!$asset) {
            flash('error', 'Activo fijo no encontrado.');
            $this->redirect('index.php?route=fixed-assets');
        }
        $this->render('fixed-assets/edit', [
            'title' => 'Editar activo fijo',
            'pageTitle' => 'Editar activo fijo',
            'asset' => $asset,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $assetId = (int)($_POST['id'] ?? 0);
        $asset = $this->db->fetch(
            'SELECT id FROM fixed_assets WHERE id = :id AND company_id = :company_id',
            ['id' => $assetId, 'company_id' => $companyId]
        );
        if (!$asset) {
            flash('error', 'Activo fijo no encontrado.');
            $this->redirect('index.php?route=fixed-assets');
        }
        $value = (float)($_POST['acquisition_value'] ?? 0);
        $accumulated = (float)($_POST['accumulated_depreciation'] ?? 0);
        $bookValue = max(0, $value - $accumulated);
        $this->assets->update($assetId, [
            'name' => trim($_POST['name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'acquisition_date' => trim($_POST['acquisition_date'] ?? date('Y-m-d')),
            'acquisition_value' => $value,
            'depreciation_method' => $_POST['depreciation_method'] ?? 'linea_recta',
            'useful_life_months' => (int)($_POST['useful_life_months'] ?? 0),
            'accumulated_depreciation' => $accumulated,
            'book_value' => $bookValue,
            'status' => $_POST['status'] ?? 'activo',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Activo fijo actualizado.');
        $this->redirect('index.php?route=fixed-assets');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $assetId = (int)($_POST['id'] ?? 0);
        $asset = $this->db->fetch(
            'SELECT id FROM fixed_assets WHERE id = :id AND company_id = :company_id',
            ['id' => $assetId, 'company_id' => $companyId]
        );
        if (!$asset) {
            flash('error', 'Activo fijo no encontrado.');
            $this->redirect('index.php?route=fixed-assets');
        }
        try {
            $this->db->execute(
                'DELETE FROM fixed_assets WHERE id = :id AND company_id = :company_id',
                ['id' => $assetId, 'company_id' => $companyId]
            );
            audit($this->db, Auth::user()['id'], 'delete', 'fixed_assets', $assetId);
            flash('success', 'Activo fijo eliminado.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete fixed asset: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar el activo fijo.');
        }
        $this->redirect('index.php?route=fixed-assets');
    }
}
