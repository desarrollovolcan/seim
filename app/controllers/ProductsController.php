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

    public function bulk(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('products/bulk', [
            'title' => 'Carga masiva de productos',
            'pageTitle' => 'Carga masiva de productos',
            'suppliers' => $this->suppliers->active($companyId),
            'families' => $this->families->active($companyId),
            'subfamilies' => $this->subfamilies->active($companyId),
            'competitors' => $this->competitors->active($companyId),
        ]);
    }

    public function bulkTemplate(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $supplier = $this->suppliers->active($companyId)[0] ?? null;
        $competitor = $this->competitors->active($companyId)[0] ?? null;
        $family = $this->families->active($companyId)[0] ?? null;
        $subfamily = $this->subfamilies->active($companyId)[0] ?? null;

        $output = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="plantilla_carga_productos.csv"');
        echo "\xEF\xBB\xBF";
        fputcsv($output, [
            'name',
            'sku',
            'description',
            'supplier_code',
            'competitor_code',
            'family_code',
            'subfamily_code',
            'supplier_price',
            'competition_price',
            'price',
            'cost',
            'stock',
            'stock_min',
            'status',
        ]);
        fputcsv($output, [
            'Producto de ejemplo',
            'SKU-001',
            'Descripción opcional',
            strtoupper(trim((string)($supplier['code'] ?? 'PRO'))),
            strtoupper(trim((string)($competitor['code'] ?? 'COM'))),
            strtoupper(trim((string)($family['code'] ?? 'FAM'))),
            strtoupper(trim((string)($subfamily['code'] ?? 'SUB'))),
            '1000',
            '1200',
            '1500',
            '800',
            '10',
            '2',
            'activo',
        ]);
        fclose($output);
        exit;
    }

    public function bulkStore(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $file = $_FILES['bulk_file'] ?? null;
        if (!$file || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('error', 'Debes seleccionar un archivo CSV válido.');
            $this->redirect('index.php?route=products/bulk');
        }

        $tmpPath = (string)($file['tmp_name'] ?? '');
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            flash('error', 'No fue posible leer el archivo subido.');
            $this->redirect('index.php?route=products/bulk');
        }

        $handle = fopen($tmpPath, 'r');
        if ($handle === false) {
            flash('error', 'No fue posible abrir el archivo.');
            $this->redirect('index.php?route=products/bulk');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            flash('error', 'El archivo está vacío.');
            $this->redirect('index.php?route=products/bulk');
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $delimiterCandidates = [',', ';', "\t"];
        $detectedDelimiter = ',';
        $bestCount = -1;
        foreach ($delimiterCandidates as $candidate) {
            $count = substr_count($firstLine, $candidate);
            if ($count > $bestCount) {
                $bestCount = $count;
                $detectedDelimiter = $candidate;
            }
        }

        rewind($handle);
        $header = fgetcsv($handle, 0, $detectedDelimiter);
        if (!$header) {
            fclose($handle);
            flash('error', 'El archivo está vacío.');
            $this->redirect('index.php?route=products/bulk');
        }
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$header[0]) ?? (string)$header[0];
        }
        $header = array_map(static fn($value): string => strtolower(trim((string)$value)), $header);
        $requiredColumnGroups = [
            'name' => ['name', 'nombre'],
            'supplier' => ['supplier_code', 'supplier', 'supplier_name', 'proveedor_codigo', 'proveedor'],
            'competitor' => ['competitor_code', 'competitor_company_code', 'competitor', 'competitor_name', 'empresa_competencia_codigo', 'empresa_competencia'],
            'family' => ['family_code', 'family', 'family_name', 'familia_codigo', 'familia'],
            'subfamily' => ['subfamily_code', 'subfamily', 'subfamily_name', 'subfamilia_codigo', 'subfamilia'],
        ];
        foreach ($requiredColumnGroups as $group => $aliases) {
            $hasOne = false;
            foreach ($aliases as $alias) {
                if (in_array($alias, $header, true)) {
                    $hasOne = true;
                    break;
                }
            }
            if (!$hasOne) {
                fclose($handle);
                flash('error', 'Falta una columna obligatoria para ' . $group . '. Aceptadas: ' . implode(', ', $aliases) . '.');
                $this->redirect('index.php?route=products/bulk');
            }
        }

        $supplierByCode = [];
        $supplierByName = [];
        foreach ($this->suppliers->active($companyId) as $supplier) {
            $supplierByCode[strtoupper(trim((string)($supplier['code'] ?? '')))] = $supplier;
            $supplierByName[strtoupper(trim((string)($supplier['name'] ?? '')))] = $supplier;
        }
        $competitorByCode = [];
        $competitorByName = [];
        foreach ($this->competitors->active($companyId) as $competitor) {
            $competitorByCode[strtoupper(trim((string)($competitor['code'] ?? '')))] = $competitor;
            $competitorByName[strtoupper(trim((string)($competitor['name'] ?? '')))] = $competitor;
        }
        $familyByCode = [];
        $familyByName = [];
        foreach ($this->families->active($companyId) as $family) {
            $familyByCode[strtoupper(trim((string)($family['code'] ?? '')))] = $family;
            $familyByName[strtoupper(trim((string)($family['name'] ?? '')))] = $family;
        }
        $subfamilyByCode = [];
        $subfamilyByName = [];
        foreach ($this->subfamilies->active($companyId) as $subfamily) {
            $subfamilyByCode[strtoupper(trim((string)($subfamily['code'] ?? '')))] = $subfamily;
            $subfamilyByName[strtoupper(trim((string)($subfamily['name'] ?? '')))] = $subfamily;
        }

        $rowNumber = 1;
        $created = 0;
        $errors = [];
        while (($row = fgetcsv($handle, 0, $detectedDelimiter)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, static fn($value): bool => trim((string)$value) !== '')) === 0) {
                continue;
            }
            $data = [];
            foreach ($header as $index => $column) {
                $data[$column] = trim((string)($row[$index] ?? ''));
            }

            $name = trim((string)($data['name'] ?? $data['nombre'] ?? ''));
            if ($name === '') {
                $errors[] = "Fila {$rowNumber}: el nombre es obligatorio.";
                continue;
            }

            $supplierCode = strtoupper((string)($data['supplier_code'] ?? $data['proveedor_codigo'] ?? ''));
            $supplierName = strtoupper((string)($data['supplier'] ?? $data['supplier_name'] ?? $data['proveedor'] ?? ''));
            $competitorCode = strtoupper((string)($data['competitor_code'] ?? $data['competitor_company_code'] ?? $data['empresa_competencia_codigo'] ?? ''));
            $competitorName = strtoupper((string)($data['competitor'] ?? $data['competitor_name'] ?? $data['empresa_competencia'] ?? ''));
            $familyCode = strtoupper((string)($data['family_code'] ?? $data['familia_codigo'] ?? ''));
            $familyName = strtoupper((string)($data['family'] ?? $data['family_name'] ?? $data['familia'] ?? ''));
            $subfamilyCode = strtoupper((string)($data['subfamily_code'] ?? $data['subfamilia_codigo'] ?? ''));
            $subfamilyName = strtoupper((string)($data['subfamily'] ?? $data['subfamily_name'] ?? $data['subfamilia'] ?? ''));

            $supplier = $supplierByCode[$supplierCode] ?? null;
            if (!$supplier && $supplierName !== '') {
                $supplier = $supplierByName[$supplierName] ?? null;
            }
            if (!$supplier) {
                $errors[] = "Fila {$rowNumber}: proveedor no encontrado (código: {$supplierCode}, nombre: {$supplierName}).";
                continue;
            }
            $competitor = $competitorByCode[$competitorCode] ?? null;
            if (!$competitor && $competitorName !== '') {
                $competitor = $competitorByName[$competitorName] ?? null;
            }
            if (!$competitor) {
                $errors[] = "Fila {$rowNumber}: empresa competencia no encontrada (código: {$competitorCode}, nombre: {$competitorName}).";
                continue;
            }
            $family = $familyByCode[$familyCode] ?? null;
            if (!$family && $familyName !== '') {
                $family = $familyByName[$familyName] ?? null;
            }
            if (!$family) {
                $errors[] = "Fila {$rowNumber}: familia no encontrada (código: {$familyCode}, nombre: {$familyName}).";
                continue;
            }
            $subfamily = $subfamilyByCode[$subfamilyCode] ?? null;
            if (!$subfamily && $subfamilyName !== '') {
                $subfamily = $subfamilyByName[$subfamilyName] ?? null;
            }
            if (!$subfamily) {
                $errors[] = "Fila {$rowNumber}: subfamilia no encontrada (código: {$subfamilyCode}, nombre: {$subfamilyName}).";
                continue;
            }
            if ((int)($subfamily['family_id'] ?? 0) !== (int)$family['id']) {
                $errors[] = "Fila {$rowNumber}: la subfamilia {$subfamilyCode} no pertenece a la familia {$familyCode}.";
                continue;
            }

            $competitionCode = $this->buildCompetitionCode($companyId, $competitor, $family, $subfamily);
            $supplierCodeGenerated = $this->buildSupplierCode($companyId, $supplier, $family, $subfamily);
            $status = strtolower((string)($data['status'] ?? 'activo')) === 'inactivo' ? 'inactivo' : 'activo';

            $this->products->create([
                'company_id' => $companyId,
                'supplier_id' => (int)$supplier['id'],
                'competitor_company_id' => (int)$competitor['id'],
                'family_id' => (int)$family['id'],
                'subfamily_id' => (int)$subfamily['id'],
                'competition_code' => $competitionCode,
                'supplier_code' => $supplierCodeGenerated,
                'supplier_price' => (float)($data['supplier_price'] ?? 0),
                'competition_price' => (float)($data['competition_price'] ?? 0),
                'name' => $name,
                'sku' => trim((string)($data['sku'] ?? '')),
                'description' => trim((string)($data['description'] ?? '')),
                'price' => (float)($data['price'] ?? 0),
                'cost' => (float)($data['cost'] ?? 0),
                'stock' => max(0, (int)($data['stock'] ?? 0)),
                'stock_min' => max(0, (int)($data['stock_min'] ?? 0)),
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }

        fclose($handle);

        if ($created > 0) {
            audit($this->db, Auth::user()['id'], 'create', 'products');
            flash('success', "Carga masiva finalizada: {$created} producto(s) creado(s).");
        }
        if (!empty($errors)) {
            $errorSummary = implode(' | ', array_slice($errors, 0, 8));
            if (count($errors) > 8) {
                $errorSummary .= ' | ...';
            }
            flash('error', 'Se detectaron filas con error: ' . $errorSummary);
        }
        if ($created === 0 && empty($errors)) {
            flash('error', 'No se encontraron filas para procesar.');
        }

        $this->redirect('index.php?route=products/bulk');
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
        $supplierPrice = (float)($_POST['supplier_price'] ?? 0);
        $competitionPrice = (float)($_POST['competition_price'] ?? 0);
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
            'supplier_price' => $supplierPrice,
            'competition_price' => $competitionPrice,
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
        $supplierPrice = (float)($_POST['supplier_price'] ?? 0);
        $competitionPrice = (float)($_POST['competition_price'] ?? 0);
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
            'supplier_price' => $supplierPrice,
            'competition_price' => $competitionPrice,
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
