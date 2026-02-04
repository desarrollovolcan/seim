<?php

class ProductsController extends Controller
{
    private ProductsModel $products;
    private SuppliersModel $suppliers;
    private ProductFamiliesModel $families;
    private ProductSubfamiliesModel $subfamilies;
    private CompetitorCompaniesModel $competitors;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->products = new ProductsModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->families = new ProductFamiliesModel($db);
        $this->subfamilies = new ProductSubfamiliesModel($db);
        $this->competitors = new CompetitorCompaniesModel($db);
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
        $competitors = $this->competitors->active($companyId);

        $this->render('products/create', [
            'title' => 'Nuevo producto',
            'pageTitle' => 'Nuevo producto',
            'suppliers' => $suppliers,
            'families' => $families,
            'subfamilies' => $subfamilies,
            'competitors' => $competitors,
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
        } else {
            flash('error', 'Selecciona un proveedor para generar el código.');
            $this->redirect('index.php?route=products/create');
        }
        $familyId = !empty($_POST['family_id']) ? (int)$_POST['family_id'] : null;
        $subfamilyId = !empty($_POST['subfamily_id']) ? (int)$_POST['subfamily_id'] : null;
        $competitorCompanyId = !empty($_POST['competitor_company_id']) ? (int)$_POST['competitor_company_id'] : null;
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
        if (!$competitorCompanyId || !$familyId || !$subfamilyId) {
            flash('error', 'Selecciona empresa competencia, familia y subfamilia para generar el código.');
            $this->redirect('index.php?route=products/create');
        }
        $competitorCompany = $this->competitors->findForCompany($competitorCompanyId, $companyId);
        if (!$competitorCompany) {
            flash('error', 'Empresa competencia no válida.');
            $this->redirect('index.php?route=products/create');
        }
        $family = $this->families->findForCompany($familyId, $companyId);
        $subfamily = $this->subfamilies->findForCompany($subfamilyId, $companyId);
        $competitionCode = $this->buildCompetitionCode($companyId, $competitorCompany, $family, $subfamily);
        $supplierCode = $this->buildSupplierCode($companyId, $supplier, $family, $subfamily);

        $this->products->create([
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
            'competitor_company_id' => $competitorCompanyId,
            'family_id' => $familyId,
            'subfamily_id' => $subfamilyId,
            'competition_code' => $competitionCode,
            'supplier_code' => $supplierCode,
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
        $competitors = $this->competitors->active($companyId);

        $this->render('products/edit', [
            'title' => 'Editar producto',
            'pageTitle' => 'Editar producto',
            'product' => $product,
            'suppliers' => $suppliers,
            'families' => $families,
            'subfamilies' => $subfamilies,
            'competitors' => $competitors,
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
        } else {
            flash('error', 'Selecciona un proveedor para generar el código.');
            $this->redirect('index.php?route=products/edit&id=' . $id);
        }
        $familyId = !empty($_POST['family_id']) ? (int)$_POST['family_id'] : null;
        $subfamilyId = !empty($_POST['subfamily_id']) ? (int)$_POST['subfamily_id'] : null;
        $competitorCompanyId = !empty($_POST['competitor_company_id']) ? (int)$_POST['competitor_company_id'] : null;
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
        if (!$competitorCompanyId || !$familyId || !$subfamilyId) {
            flash('error', 'Selecciona empresa competencia, familia y subfamilia para generar el código.');
            $this->redirect('index.php?route=products/edit&id=' . $id);
        }
        $competitorCompany = $this->competitors->findForCompany($competitorCompanyId, $companyId);
        if (!$competitorCompany) {
            flash('error', 'Empresa competencia no válida.');
            $this->redirect('index.php?route=products/edit&id=' . $id);
        }
        $family = $this->families->findForCompany($familyId, $companyId);
        $subfamily = $this->subfamilies->findForCompany($subfamilyId, $companyId);
        $competitionCode = $this->buildCompetitionCode(
            $companyId,
            $competitorCompany,
            $family,
            $subfamily,
            $product['competition_code'] ?? null,
            (int)$product['id']
        );
        $supplierCode = $this->buildSupplierCode(
            $companyId,
            $supplier,
            $family,
            $subfamily,
            $product['supplier_code'] ?? null,
            (int)$product['id']
        );

        $this->products->update($id, [
            'supplier_id' => $supplierId,
            'competitor_company_id' => $competitorCompanyId,
            'family_id' => $familyId,
            'subfamily_id' => $subfamilyId,
            'competition_code' => $competitionCode,
            'supplier_code' => $supplierCode,
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

    private function buildCompetitionCode(
        int $companyId,
        array $competitorCompany,
        array $family,
        array $subfamily,
        ?string $currentCode = null,
        ?int $excludeProductId = null
    ): string {
        $competitorCode = strtoupper(trim($competitorCompany['code'] ?? ''));
        $familyCode = strtoupper(trim($family['code'] ?? ''));
        $subfamilyCode = strtoupper(trim($subfamily['code'] ?? ''));
        $prefix = "{$competitorCode}-{$familyCode}-{$subfamilyCode}-";

        if ($currentCode && str_starts_with($currentCode, $prefix)) {
            return $currentCode;
        }

        $lastCode = $this->products->latestCompetitionCode($companyId, $prefix, $excludeProductId);
        $sequence = 1;
        if ($lastCode) {
            $parts = explode('-', $lastCode);
            $lastSequence = (int)array_pop($parts);
            if ($lastSequence > 0) {
                $sequence = $lastSequence + 1;
            }
        }

        $sequenceFormatted = str_pad((string)$sequence, 4, '0', STR_PAD_LEFT);
        return $prefix . $sequenceFormatted;
    }

    private function buildSupplierCode(
        int $companyId,
        array $supplier,
        array $family,
        array $subfamily,
        ?string $currentCode = null,
        ?int $excludeProductId = null
    ): string {
        $supplierCode = strtoupper(trim($supplier['code'] ?? ''));
        $familyCode = strtoupper(trim($family['code'] ?? ''));
        $subfamilyCode = strtoupper(trim($subfamily['code'] ?? ''));
        $prefix = "{$supplierCode}-{$familyCode}-{$subfamilyCode}-";

        if ($currentCode && str_starts_with($currentCode, $prefix)) {
            return $currentCode;
        }

        $lastCode = $this->products->latestSupplierCode($companyId, $prefix, $excludeProductId);
        $sequence = 1;
        if ($lastCode) {
            $parts = explode('-', $lastCode);
            $lastSequence = (int)array_pop($parts);
            if ($lastSequence > 0) {
                $sequence = $lastSequence + 1;
            }
        }

        $sequenceFormatted = str_pad((string)$sequence, 4, '0', STR_PAD_LEFT);
        return $prefix . $sequenceFormatted;
    }
}
