<?php

class ProductsController extends Controller
{
    private ProductsModel $products;
    private SuppliersModel $suppliers;
    private CompaniesModel $companies;
    private ProductFamiliesModel $families;
    private ProductSubfamiliesModel $subfamilies;
    private CompetitorCompaniesModel $competitors;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->products = new ProductsModel($db);
        $this->suppliers = new SuppliersModel($db);
        $this->companies = new CompaniesModel($db);
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
        $filters = [
            'search' => trim((string)($_GET['search'] ?? '')),
            'family_id' => (int)($_GET['family_id'] ?? 0),
            'subfamily_id' => (int)($_GET['subfamily_id'] ?? 0),
            'supplier_id' => (int)($_GET['supplier_id'] ?? 0),
        ];
        $products = $this->products->filtered($companyId, $filters);

        $this->render('products/index', [
            'title' => 'Productos',
            'pageTitle' => 'Inventario de productos',
            'products' => $products,
            'filters' => $filters,
            'families' => $this->families->active($companyId),
            'subfamilies' => $this->subfamilies->active($companyId),
            'suppliers' => $this->suppliers->active($companyId),
        ]);
    }

    public function bulkAssign(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $filters = [
            'search' => trim((string)($_POST['filter_search'] ?? '')),
            'family_id' => (int)($_POST['filter_family_id'] ?? 0),
            'subfamily_id' => (int)($_POST['filter_subfamily_id'] ?? 0),
            'supplier_id' => (int)($_POST['filter_supplier_id'] ?? 0),
        ];
        $redirectUrl = $this->productsIndexUrl($filters);

        $scope = (string)($_POST['bulk_scope'] ?? 'selected');
        if ($scope === 'filtered') {
            $productIds = $this->products->filteredIds($companyId, $filters);
        } else {
            $rawIds = $_POST['product_ids'] ?? [];
            $productIds = array_values(array_filter(array_map('intval', is_array($rawIds) ? $rawIds : []), static fn(int $id): bool => $id > 0));
        }

        if ($productIds === []) {
            flash('error', $scope === 'filtered' ? 'No hay productos que coincidan con los filtros actuales.' : 'Debes seleccionar al menos un producto.');
            $this->redirect($redirectUrl);
        }

        $familyId = (int)($_POST['bulk_family_id'] ?? 0);
        $subfamilyId = (int)($_POST['bulk_subfamily_id'] ?? 0);
        $supplierId = (int)($_POST['bulk_supplier_id'] ?? 0);
        if ($familyId <= 0 && $subfamilyId <= 0 && $supplierId <= 0) {
            flash('error', 'Selecciona al menos una categoría, subcategoría o proveedor para asignar por lote.');
            $this->redirect($redirectUrl);
        }

        $updated = $this->products->bulkAssign(
            $companyId,
            $productIds,
            $familyId > 0 ? $familyId : null,
            $subfamilyId > 0 ? $subfamilyId : null,
            $supplierId > 0 ? $supplierId : null
        );

        $scopeLabel = $scope === 'filtered' ? 'productos filtrados' : 'productos seleccionados';
        flash('success', 'Asignación por lote aplicada a ' . (int)$updated . ' ' . $scopeLabel . '.');
        $this->redirect($redirectUrl);
    }


    private function productsIndexUrl(array $filters = []): string
    {
        $params = ['route' => 'products'];
        foreach (['search', 'family_id', 'subfamily_id', 'supplier_id'] as $key) {
            $value = $filters[$key] ?? null;
            if ($value !== null && $value !== '' && (string)$value !== '0') {
                $params[$key] = $value;
            }
        }

        unset($params['route']);
        $query = http_build_query($params);

        return 'apps-productos.php' . ($query !== '' ? '?' . $query : '');
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
        $jobId = trim((string)($_GET['job_id'] ?? ''));

        $this->render('products/bulk', [
            'title' => 'Carga masiva de productos',
            'pageTitle' => 'Carga masiva de productos',
            'suppliers' => $this->suppliers->active($companyId),
            'companies' => $this->companies->active(),
            'families' => $this->families->active($companyId),
            'subfamilies' => $this->subfamilies->active($companyId),
            'bulkJobId' => $jobId,
        ]);
    }

    public function bulkStart(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $selectedCompanyId = (int)($_POST['default_competitor_company_id'] ?? 0);
        $defaultCompetitor = $this->resolveDefaultCompetitorCompany($companyId, $selectedCompanyId);
        if (!$defaultCompetitor) {
            flash('error', 'Selecciona una empresa para usarla como competencia en la carga masiva.');
            $this->redirect('index.php?route=products/bulk');
        }

        $file = $_FILES['bulk_file'] ?? null;
        if (!$file || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('error', 'Debes seleccionar un archivo CSV válido.');
            $this->redirect('index.php?route=products/bulk');
        }

        $originalName = (string)($file['name'] ?? 'import.csv');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $basePath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'seim_products_import_' . uniqid('', true);
        $uploadedPath = $basePath . '.' . ($extension !== '' ? $extension : 'csv');
        if (!move_uploaded_file((string)($file['tmp_name'] ?? ''), $uploadedPath)) {
            flash('error', 'No fue posible preparar el archivo para la carga masiva.');
            $this->redirect('index.php?route=products/bulk');
        }

        $destination = $uploadedPath;
        if ($extension === 'xlsx') {
            $fileSize = (int)($file['size'] ?? 0);
            if ($fileSize > 5 * 1024 * 1024) {
                @unlink($uploadedPath);
                flash('error', 'El archivo XLSX es muy grande para procesarlo en web sin timeout. Guárdalo como CSV UTF-8 y vuelve a subirlo.');
                $this->redirect('index.php?route=products/bulk');
            }
            $csvPath = $basePath . '.csv';
            if (!$this->convertXlsxToCsv($uploadedPath, $csvPath)) {
                @unlink($uploadedPath);
                flash('error', 'No fue posible leer el archivo XLSX. Guarda el archivo en CSV UTF-8 o revisa que no esté dañado.');
                $this->redirect('index.php?route=products/bulk');
            }
            @unlink($uploadedPath);
            $destination = $csvPath;
        }

        $handle = fopen($destination, 'r');
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
        fclose($handle);
        if (!$header) {
            flash('error', 'El archivo está vacío.');
            $this->redirect('index.php?route=products/bulk');
        }
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$header[0]) ?? (string)$header[0];
        }
        $header = array_map(static fn($value): string => strtolower(trim((string)$value)), $header);

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $supplierByCode = [];
        $supplierByName = [];
        foreach ($this->suppliers->active($companyId) as $supplier) {
            $supplierByCode[strtoupper(trim((string)($supplier['code'] ?? '')))] = $supplier;
            $supplierByName[strtoupper(trim((string)($supplier['name'] ?? '')))] = $supplier;
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

        $handle = fopen($destination, 'r');
        if ($handle === false) {
            flash('error', 'No fue posible abrir el archivo para importar.');
            $this->redirect('index.php?route=products/bulk');
        }
        // descartar header
        fgetcsv($handle, 0, $detectedDelimiter);

        $created = 0;
        $errors = [];
        $pdo = $this->db->pdo();
        $pdo->beginTransaction();
        try {
            $rowNumber = 1;
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
                $sku = trim((string)($data['sku'] ?? $data['codigo_sku'] ?? $data['codigo'] ?? ''));
                if ($name === '' || $sku === '') {
                    $errors[] = "Fila {$rowNumber}: nombre o SKU faltante.";
                    continue;
                }

                $supplierCode = strtoupper((string)($data['supplier_code'] ?? $data['proveedor_codigo'] ?? ''));
                $supplierName = strtoupper((string)($data['supplier'] ?? $data['supplier_name'] ?? $data['proveedor'] ?? ''));
                $familyCode = strtoupper((string)($data['family_code'] ?? $data['familia_codigo'] ?? ''));
                $familyName = trim((string)($data['family'] ?? $data['family_name'] ?? $data['familia'] ?? ''));
                $subfamilyCode = strtoupper((string)($data['subfamily_code'] ?? $data['subfamilia_codigo'] ?? ''));
                $subfamilyName = trim((string)($data['subfamily'] ?? $data['subfamily_name'] ?? $data['subfamilia'] ?? ''));

                $supplier = null;
                if ($supplierCode !== '' || $supplierName !== '') {
                    $supplier = $supplierByCode[$supplierCode] ?? $supplierByName[$supplierCode] ?? $supplierByName[$supplierName] ?? null;
                }

                if ($familyName === '' && ($familyCode !== '' && (strlen($familyCode) > 3 || str_contains($familyCode, ' ')))) {
                    $familyName = $familyCode;
                    $familyCode = '';
                }
                if ($subfamilyName === '' && ($subfamilyCode !== '' && (strlen($subfamilyCode) > 3 || str_contains($subfamilyCode, ' ')))) {
                    $subfamilyName = $subfamilyCode;
                    $subfamilyCode = '';
                }

                $family = null;
                if ($familyCode !== '' || strtoupper($familyName) !== '') {
                    $family = $this->resolveOrCreateFamily($companyId, $familyCode, $familyName, $familyByCode, $familyByName);
                }
                $subfamily = null;
                if ($subfamilyCode !== '' || strtoupper($subfamilyName) !== '') {
                    if (!$family) {
                        $familyFallbackName = $familyName !== '' ? $familyName : ('Familia ' . ($subfamilyName !== '' ? $subfamilyName : $subfamilyCode));
                        $family = $this->resolveOrCreateFamily($companyId, $familyCode, $familyFallbackName, $familyByCode, $familyByName);
                    }
                    if ($family) {
                        $subfamily = $this->resolveOrCreateSubfamily($companyId, (int)$family['id'], $subfamilyCode, $subfamilyName, $subfamilyByCode, $subfamilyByName);
                    }
                }

                $this->products->create([
                    'company_id' => $companyId,
                    'supplier_id' => $supplier ? (int)$supplier['id'] : null,
                    'competitor_company_id' => (int)$defaultCompetitor['id'],
                    'family_id' => $family ? (int)$family['id'] : null,
                    'subfamily_id' => $subfamily ? (int)$subfamily['id'] : null,
                    'competition_code' => ($family && $subfamily) ? $this->buildCompetitionCode($companyId, $defaultCompetitor, $family, $subfamily) : null,
                    'supplier_code' => ($supplier && $family && $subfamily) ? $this->buildSupplierCode($companyId, $supplier, $family, $subfamily) : null,
                    'supplier_price' => (float)($data['supplier_price'] ?? 0),
                    'competition_price' => (float)($data['competition_price'] ?? 0),
                    'name' => $name,
                    'sku' => $sku,
                    'description' => trim((string)($data['description'] ?? '')),
                    'price' => (float)($data['price'] ?? 0),
                    'cost' => (float)($data['cost'] ?? 0),
                    'stock' => max(0, (int)($data['stock'] ?? 0)),
                    'stock_min' => max(0, (int)($data['stock_min'] ?? 0)),
                    'status' => strtolower((string)($data['status'] ?? 'activo')) === 'inactivo' ? 'inactivo' : 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $created++;
            }
            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            fclose($handle);
            @unlink($destination);
            log_message('error', 'Error carga masiva directa: ' . $exception->getMessage());
            flash('error', 'No se pudo completar la carga masiva: ' . $exception->getMessage());
            $this->redirect('index.php?route=products/bulk');
        }
        fclose($handle);
        @unlink($destination);

        if ($created > 0) {
            audit($this->db, Auth::user()['id'], 'create', 'products');
            flash('success', "Carga masiva completada: {$created} producto(s) creados.");
        }
        if (!empty($errors)) {
            flash('error', 'Filas con observaciones: ' . implode(' | ', array_slice($errors, 0, 8)) . (count($errors) > 8 ? ' | ...' : ''));
        }
        $this->redirect('index.php?route=products/bulk');
    }

    public function bulkProcess(): void
    {
        $this->requireLogin();
        $token = (string)($_POST['csrf_token'] ?? '');
        if ($token === '' || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), $token)) {
            $this->jsonBulk(['ok' => false, 'message' => 'CSRF token inválido para continuar la carga. Recarga la página e inténtalo de nuevo.']);
            return;
        }
        $companyId = $this->requireCompany();
        try {
            $jobId = trim((string)($_POST['job_id'] ?? ''));
            $jobs = $_SESSION['products_bulk_jobs'] ?? [];
            $job = $jobs[$jobId] ?? null;
            if (!$job || (int)($job['company_id'] ?? 0) !== $companyId) {
                $this->jsonBulk(['ok' => false, 'message' => 'Proceso de carga no válido.']);
                return;
            }

            $path = (string)($job['path'] ?? '');
            if ($path === '' || !is_file($path)) {
                $this->jsonBulk(['ok' => false, 'message' => 'El archivo temporal no existe.']);
                return;
            }

            $delimiter = (string)($job['delimiter'] ?? ',');
            $header = (array)($job['header'] ?? []);
            $nextLine = (int)($job['next_line'] ?? 1);
            $chunkSize = 25;
            $chunkTimeLimit = 1.5;

            $defaultCompetitor = $this->competitors->findForCompany((int)($job['competitor_company_id'] ?? 0), $companyId);
            if (!$defaultCompetitor) {
                $this->jsonBulk(['ok' => false, 'message' => 'No se encontró empresa competencia para el proceso.']);
                return;
            }

            $supplierByCode = [];
            $supplierByName = [];
            foreach ($this->suppliers->active($companyId) as $supplier) {
                $supplierByCode[strtoupper(trim((string)($supplier['code'] ?? '')))] = $supplier;
                $supplierByName[strtoupper(trim((string)($supplier['name'] ?? '')))] = $supplier;
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

            $file = new SplFileObject($path, 'r');
            $file->setFlags(SplFileObject::READ_CSV);
            $file->setCsvControl($delimiter);
            $file->seek($nextLine);

            $processedInChunk = 0;
            $startedAt = microtime(true);
            while (
                !$file->eof()
                && $processedInChunk < $chunkSize
                && (microtime(true) - $startedAt) < $chunkTimeLimit
            ) {
            $row = $file->current();
            $file->next();
            $nextLine++;
            $processedInChunk++;
            $job['processed_rows'] = (int)($job['processed_rows'] ?? 0) + 1;

            if (!is_array($row) || count(array_filter($row, static fn($value): bool => trim((string)$value) !== '')) === 0) {
                continue;
            }

            $data = [];
            foreach ($header as $index => $column) {
                $data[$column] = trim((string)($row[$index] ?? ''));
            }
            $name = trim((string)($data['name'] ?? $data['nombre'] ?? ''));
            $sku = trim((string)($data['sku'] ?? $data['codigo_sku'] ?? $data['codigo'] ?? ''));
            if ($name === '' || $sku === '') {
                $job['errors'][] = 'Fila ' . $nextLine . ': nombre o SKU faltante.';
                continue;
            }

            $supplierCode = strtoupper((string)($data['supplier_code'] ?? $data['proveedor_codigo'] ?? ''));
            $supplierName = strtoupper((string)($data['supplier'] ?? $data['supplier_name'] ?? $data['proveedor'] ?? ''));
            $familyCode = strtoupper((string)($data['family_code'] ?? $data['familia_codigo'] ?? ''));
            $familyName = trim((string)($data['family'] ?? $data['family_name'] ?? $data['familia'] ?? ''));
            $subfamilyCode = strtoupper((string)($data['subfamily_code'] ?? $data['subfamilia_codigo'] ?? ''));
            $subfamilyName = trim((string)($data['subfamily'] ?? $data['subfamily_name'] ?? $data['subfamilia'] ?? ''));

            $supplier = null;
            if ($supplierCode !== '' || $supplierName !== '') {
                $supplier = $supplierByCode[$supplierCode] ?? null;
                if (!$supplier && $supplierCode !== '') {
                    $supplier = $supplierByName[$supplierCode] ?? null;
                }
                if (!$supplier && $supplierName !== '') {
                    $supplier = $supplierByName[$supplierName] ?? null;
                }
            }

            if ($familyName === '' && ($familyCode !== '' && (strlen($familyCode) > 3 || str_contains($familyCode, ' ')))) {
                $familyName = $familyCode;
                $familyCode = '';
            }
            if ($subfamilyName === '' && ($subfamilyCode !== '' && (strlen($subfamilyCode) > 3 || str_contains($subfamilyCode, ' ')))) {
                $subfamilyName = $subfamilyCode;
                $subfamilyCode = '';
            }

            $family = null;
            if ($familyCode !== '' || strtoupper($familyName) !== '') {
                $family = $this->resolveOrCreateFamily($companyId, $familyCode, $familyName, $familyByCode, $familyByName);
            }
            $subfamily = null;
            if ($subfamilyCode !== '' || strtoupper($subfamilyName) !== '') {
                if (!$family) {
                    $familyFallbackName = $familyName !== '' ? $familyName : ('Familia ' . ($subfamilyName !== '' ? $subfamilyName : $subfamilyCode));
                    $family = $this->resolveOrCreateFamily($companyId, $familyCode, $familyFallbackName, $familyByCode, $familyByName);
                }
                if ($family) {
                    $subfamily = $this->resolveOrCreateSubfamily($companyId, (int)$family['id'], $subfamilyCode, $subfamilyName, $subfamilyByCode, $subfamilyByName);
                }
            }

            $competitionCode = ($family && $subfamily) ? $this->buildCompetitionCode($companyId, $defaultCompetitor, $family, $subfamily) : null;
            $supplierCodeGenerated = ($supplier && $family && $subfamily) ? $this->buildSupplierCode($companyId, $supplier, $family, $subfamily) : null;
            $status = strtolower((string)($data['status'] ?? 'activo')) === 'inactivo' ? 'inactivo' : 'activo';

            $this->products->create([
                'company_id' => $companyId,
                'supplier_id' => $supplier ? (int)$supplier['id'] : null,
                'competitor_company_id' => (int)$defaultCompetitor['id'],
                'family_id' => $family ? (int)$family['id'] : null,
                'subfamily_id' => $subfamily ? (int)$subfamily['id'] : null,
                'competition_code' => $competitionCode,
                'supplier_code' => $supplierCodeGenerated,
                'supplier_price' => (float)($data['supplier_price'] ?? 0),
                'competition_price' => (float)($data['competition_price'] ?? 0),
                'name' => $name,
                'sku' => $sku,
                'description' => trim((string)($data['description'] ?? '')),
                'price' => (float)($data['price'] ?? 0),
                'cost' => (float)($data['cost'] ?? 0),
                'stock' => max(0, (int)($data['stock'] ?? 0)),
                'stock_min' => max(0, (int)($data['stock_min'] ?? 0)),
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $job['created_rows'] = (int)($job['created_rows'] ?? 0) + 1;
            }

            $job['next_line'] = $nextLine;
            if ($file->eof()) {
                $job['done'] = true;
                @unlink($path);
                audit($this->db, Auth::user()['id'], 'create', 'products');
                flash('success', 'Carga masiva finalizada: ' . (int)$job['created_rows'] . ' producto(s) creados.');
                if (!empty($job['errors'])) {
                    flash('error', 'Se detectaron filas con error: ' . implode(' | ', array_slice((array)$job['errors'], 0, 8)));
                }
                unset($_SESSION['products_bulk_jobs'][$jobId]);
            } else {
                $_SESSION['products_bulk_jobs'][$jobId] = $job;
            }

            $total = max(1, (int)($job['total_rows'] ?? 1));
            $processed = (int)($job['processed_rows'] ?? 0);
            $progress = min(100, (int)round(($processed / $total) * 100));
            $this->jsonBulk([
                'ok' => true,
                'done' => (bool)($job['done'] ?? false),
                'processed' => $processed,
                'created' => (int)($job['created_rows'] ?? 0),
                'total' => $total,
                'progress' => $progress,
            ]);
        } catch (Throwable $exception) {
            log_message('error', 'bulkProcess error: ' . $exception->getMessage());
            $this->jsonBulk([
                'ok' => false,
                'message' => 'Error interno durante la carga: ' . $exception->getMessage(),
            ]);
        }
    }

    private function jsonBulk(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function convertXlsxToCsv(string $xlsxPath, string $csvPath): bool
    {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        $zip = new ZipArchive();
        if ($zip->open($xlsxPath) !== true) {
            return false;
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sharedDoc = new DOMDocument();
            if (@$sharedDoc->loadXML($sharedXml)) {
                $sharedXpath = new DOMXPath($sharedDoc);
                $items = $sharedXpath->query('//*[local-name()="si"]');
                if ($items) {
                    foreach ($items as $item) {
                        $parts = $sharedXpath->query('.//*[local-name()="t"]', $item);
                        $text = '';
                        if ($parts) {
                            foreach ($parts as $part) {
                                $text .= $part->textContent;
                            }
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                    $sheetXml = $zip->getFromName($name);
                    break;
                }
            }
        }
        $zip->close();
        if ($sheetXml === false) {
            return false;
        }

        $sheetDoc = new DOMDocument();
        if (!@$sheetDoc->loadXML($sheetXml)) {
            return false;
        }
        $xpath = new DOMXPath($sheetDoc);
        $rows = $xpath->query('//*[local-name()="sheetData"]/*[local-name()="row"]');
        if (!$rows) {
            return false;
        }

        $output = fopen($csvPath, 'w');
        if ($output === false) {
            return false;
        }

        foreach ($rows as $row) {
            $rowData = [];
            $cells = $xpath->query('./*[local-name()="c"]', $row);
            if (!$cells) {
                continue;
            }
            foreach ($cells as $cell) {
                $cellRef = (string)$cell->getAttribute('r');
                $columnIndex = $this->columnIndexFromCellRef($cellRef);
                $type = (string)$cell->getAttribute('t');
                $value = '';

                if ($type === 's') {
                    $valueNodes = $xpath->query('./*[local-name()="v"]', $cell);
                    $sharedIndex = $valueNodes && $valueNodes->length > 0 ? (int)$valueNodes->item(0)->textContent : 0;
                    $value = (string)($sharedStrings[$sharedIndex] ?? '');
                } elseif ($type === 'inlineStr') {
                    $inlineNodes = $xpath->query('./*[local-name()="is"]//*[local-name()="t"]', $cell);
                    if ($inlineNodes) {
                        foreach ($inlineNodes as $inlineNode) {
                            $value .= $inlineNode->textContent;
                        }
                    }
                } else {
                    $valueNodes = $xpath->query('./*[local-name()="v"]', $cell);
                    $value = $valueNodes && $valueNodes->length > 0 ? (string)$valueNodes->item(0)->textContent : '';
                }
                if ($columnIndex >= 0) {
                    $rowData[$columnIndex] = $value;
                }
            }
            if (!empty($rowData)) {
                ksort($rowData);
                $lastIndex = max(array_keys($rowData));
                $ordered = array_fill(0, $lastIndex + 1, '');
                foreach ($rowData as $index => $value) {
                    $ordered[$index] = $value;
                }
                fputcsv($output, $ordered);
            }
        }
        fclose($output);
        return true;
    }

    private function columnIndexFromCellRef(string $cellRef): int
    {
        if ($cellRef === '') {
            return -1;
        }
        if (!preg_match('/^([A-Z]+)/', strtoupper($cellRef), $matches)) {
            return -1;
        }
        $letters = $matches[1];
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    public function bulkTemplate(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $supplier = $this->suppliers->active($companyId)[0] ?? null;
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
        $this->bulkStart();
        return;

        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');
        $selectedCompanyId = (int)($_POST['default_competitor_company_id'] ?? 0);
        $defaultCompetitor = $this->resolveDefaultCompetitorCompany($companyId, $selectedCompanyId);
        if (!$defaultCompetitor) {
            flash('error', 'Selecciona una empresa para usarla como competencia en la carga masiva.');
            $this->redirect('index.php?route=products/bulk');
        }

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
            'sku' => ['sku', 'codigo_sku', 'codigo'],
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
        $pdo = $this->db->pdo();
        $pdo->beginTransaction();
        try {
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
                $sku = trim((string)($data['sku'] ?? $data['codigo_sku'] ?? $data['codigo'] ?? ''));
                if ($name === '') {
                    $errors[] = "Fila {$rowNumber}: el nombre es obligatorio.";
                    continue;
                }
                if ($sku === '') {
                    $errors[] = "Fila {$rowNumber}: el SKU es obligatorio.";
                    continue;
                }

                $supplierCode = strtoupper((string)($data['supplier_code'] ?? $data['proveedor_codigo'] ?? ''));
                $supplierName = strtoupper((string)($data['supplier'] ?? $data['supplier_name'] ?? $data['proveedor'] ?? ''));
                $familyCode = strtoupper((string)($data['family_code'] ?? $data['familia_codigo'] ?? ''));
                $familyName = trim((string)($data['family'] ?? $data['family_name'] ?? $data['familia'] ?? ''));
                $subfamilyCode = strtoupper((string)($data['subfamily_code'] ?? $data['subfamilia_codigo'] ?? ''));
                $subfamilyName = trim((string)($data['subfamily'] ?? $data['subfamily_name'] ?? $data['subfamilia'] ?? ''));

                $supplier = null;
                if ($supplierCode !== '' || $supplierName !== '') {
                    $supplier = $supplierByCode[$supplierCode] ?? null;
                    if (!$supplier && $supplierName !== '') {
                        $supplier = $supplierByName[$supplierName] ?? null;
                    }
                }

                $family = null;
                $familyLookupName = strtoupper($familyName);
                if ($familyCode !== '' || $familyLookupName !== '') {
                    $family = $this->resolveOrCreateFamily(
                        $companyId,
                        $familyCode,
                        $familyName,
                        $familyByCode,
                        $familyByName
                    );
                }

                $subfamily = null;
                $subfamilyLookupName = strtoupper($subfamilyName);
                if ($subfamilyCode !== '' || $subfamilyLookupName !== '') {
                    if (!$family) {
                        $familyFallbackName = $familyName !== '' ? $familyName : ('Familia ' . ($subfamilyName !== '' ? $subfamilyName : $subfamilyCode));
                        $family = $this->resolveOrCreateFamily(
                            $companyId,
                            $familyCode,
                            $familyFallbackName,
                            $familyByCode,
                            $familyByName
                        );
                    }
                    if ($family) {
                        $subfamily = $this->resolveOrCreateSubfamily(
                            $companyId,
                            (int)$family['id'],
                            $subfamilyCode,
                            $subfamilyName,
                            $subfamilyByCode,
                            $subfamilyByName
                        );
                    }
                }

                $competitionCode = null;
                if ($family && $subfamily) {
                    $competitionCode = $this->buildCompetitionCode($companyId, $defaultCompetitor, $family, $subfamily);
                }
                $supplierCodeGenerated = null;
                if ($supplier && $family && $subfamily) {
                    $supplierCodeGenerated = $this->buildSupplierCode($companyId, $supplier, $family, $subfamily);
                }
                $status = strtolower((string)($data['status'] ?? 'activo')) === 'inactivo' ? 'inactivo' : 'activo';

                $this->products->create([
                    'company_id' => $companyId,
                    'supplier_id' => $supplier ? (int)$supplier['id'] : null,
                    'competitor_company_id' => (int)$defaultCompetitor['id'],
                    'family_id' => $family ? (int)$family['id'] : null,
                    'subfamily_id' => $subfamily ? (int)$subfamily['id'] : null,
                    'competition_code' => $competitionCode,
                    'supplier_code' => $supplierCodeGenerated,
                    'supplier_price' => (float)($data['supplier_price'] ?? 0),
                    'competition_price' => (float)($data['competition_price'] ?? 0),
                    'name' => $name,
                    'sku' => $sku,
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
            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            fclose($handle);
            log_message('error', 'Error en carga masiva de productos: ' . $exception->getMessage());
            flash('error', 'La carga masiva no pudo completarse por tiempo o volumen. Divide el archivo en bloques más pequeños (ej. 500 filas).');
            $this->redirect('index.php?route=products/bulk');
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

    private function resolveDefaultCompetitorCompany(int $companyId, int $selectedCompanyId): ?array
    {
        if ($selectedCompanyId <= 0) {
            return null;
        }

        $selectedCompany = $this->db->fetch(
            'SELECT * FROM companies WHERE id = :id LIMIT 1',
            ['id' => $selectedCompanyId]
        );
        if (!$selectedCompany) {
            return null;
        }

        $competitor = $this->db->fetch(
            'SELECT * FROM competitor_companies WHERE company_id = :company_id AND name = :name LIMIT 1',
            [
                'company_id' => $companyId,
                'name' => trim((string)($selectedCompany['name'] ?? '')),
            ]
        );
        if ($competitor) {
            return $competitor;
        }

        $name = trim((string)($selectedCompany['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $normalizedName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        if ($normalizedName === false) {
            $normalizedName = $name;
        }
        $codeBase = strtoupper(preg_replace('/[^A-Z0-9]/', '', $normalizedName) ?? '');
        if ($codeBase === '') {
            $codeBase = 'EMP';
        }
        $code = substr($codeBase, 0, 4);
        $index = 1;
        while ($this->db->fetch(
            'SELECT id FROM competitor_companies WHERE company_id = :company_id AND code = :code LIMIT 1',
            ['company_id' => $companyId, 'code' => $code]
        )) {
            $index++;
            $code = substr($codeBase, 0, 3) . $index;
        }

        $competitorId = $this->competitors->create([
            'company_id' => $companyId,
            'name' => $name,
            'code' => $code,
            'rut' => $selectedCompany['rut'] ?? null,
            'email' => $selectedCompany['email'] ?? null,
            'address' => $selectedCompany['address'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->competitors->findForCompany((int)$competitorId, $companyId);
    }

    private function resolveOrCreateFamily(
        int $companyId,
        string $familyCode,
        string $familyName,
        array &$familyByCode,
        array &$familyByName
    ): ?array {
        $familyName = trim($familyName);
        $familyCode = strtoupper(trim($familyCode));
        $familyLookupName = strtoupper($familyName);

        $family = $familyCode !== '' ? ($familyByCode[$familyCode] ?? null) : null;
        if (!$family && $familyLookupName !== '') {
            $family = $familyByName[$familyLookupName] ?? null;
        }
        if ($family) {
            return $family;
        }

        if ($familyName === '' && $familyCode === '') {
            return null;
        }
        if ($familyName === '') {
            $familyName = 'Familia ' . $familyCode;
            $familyLookupName = strtoupper($familyName);
        }

        $code = $familyCode !== '' ? substr(preg_replace('/[^A-Z0-9]/', '', $familyCode) ?: '', 0, 3) : '';
        if ($code === '') {
            $code = generate_three_letter_code($familyName);
        }
        while ($this->db->fetch(
            'SELECT id FROM product_families WHERE company_id = :company_id AND code = :code LIMIT 1',
            ['company_id' => $companyId, 'code' => $code]
        )) {
            $code = generate_three_letter_code($familyName . rand(1, 9));
        }

        $familyId = $this->families->create([
            'company_id' => $companyId,
            'name' => $familyName,
            'code' => $code,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $family = $this->families->findForCompany((int)$familyId, $companyId);
        if ($family) {
            $familyByCode[strtoupper((string)$family['code'])] = $family;
            $familyByName[strtoupper((string)$family['name'])] = $family;
        }
        return $family;
    }

    private function resolveOrCreateSubfamily(
        int $companyId,
        int $familyId,
        string $subfamilyCode,
        string $subfamilyName,
        array &$subfamilyByCode,
        array &$subfamilyByName
    ): ?array {
        $subfamilyCode = strtoupper(trim($subfamilyCode));
        $subfamilyName = trim($subfamilyName);
        $subfamilyLookupName = strtoupper($subfamilyName);

        $subfamily = $subfamilyCode !== '' ? ($subfamilyByCode[$subfamilyCode] ?? null) : null;
        if ($subfamily && (int)$subfamily['family_id'] !== $familyId) {
            $subfamily = null;
        }
        if (!$subfamily && $subfamilyLookupName !== '') {
            $candidate = $subfamilyByName[$subfamilyLookupName] ?? null;
            if ($candidate && (int)$candidate['family_id'] === $familyId) {
                $subfamily = $candidate;
            }
        }
        if ($subfamily) {
            return $subfamily;
        }

        if ($subfamilyName === '' && $subfamilyCode === '') {
            return null;
        }
        if ($subfamilyName === '') {
            $subfamilyName = 'Subfamilia ' . $subfamilyCode;
            $subfamilyLookupName = strtoupper($subfamilyName);
        }

        $code = $subfamilyCode !== '' ? substr(preg_replace('/[^A-Z0-9]/', '', $subfamilyCode) ?: '', 0, 3) : '';
        if ($code === '') {
            $code = generate_three_letter_code($subfamilyName);
        }
        while ($this->db->fetch(
            'SELECT id FROM product_subfamilies WHERE company_id = :company_id AND family_id = :family_id AND code = :code LIMIT 1',
            ['company_id' => $companyId, 'family_id' => $familyId, 'code' => $code]
        )) {
            $code = generate_three_letter_code($subfamilyName . rand(1, 9));
        }

        $subfamilyId = $this->subfamilies->create([
            'company_id' => $companyId,
            'family_id' => $familyId,
            'name' => $subfamilyName,
            'code' => $code,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $subfamily = $this->subfamilies->findForCompany((int)$subfamilyId, $companyId);
        if ($subfamily) {
            $subfamilyByCode[strtoupper((string)$subfamily['code'])] = $subfamily;
            $subfamilyByName[strtoupper((string)$subfamily['name'])] = $subfamily;
        }
        return $subfamily;
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
        $photo1Result = upload_product_image($_FILES['photo_1'] ?? null, 'product-photo-1');
        if (!empty($photo1Result['error'])) {
            flash('error', (string)$photo1Result['error']);
            $this->redirect('index.php?route=products/create');
        }
        $photo2Result = upload_product_image($_FILES['photo_2'] ?? null, 'product-photo-2');
        if (!empty($photo2Result['error'])) {
            flash('error', (string)$photo2Result['error']);
            $this->redirect('index.php?route=products/create');
        }

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
            'photo_1' => $photo1Result['path'],
            'photo_2' => $photo2Result['path'],
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
        $photo1Result = upload_product_image($_FILES['photo_1'] ?? null, 'product-photo-1');
        if (!empty($photo1Result['error'])) {
            flash('error', (string)$photo1Result['error']);
            $this->redirect('index.php?route=products/edit&id=' . $id);
        }
        $photo2Result = upload_product_image($_FILES['photo_2'] ?? null, 'product-photo-2');
        if (!empty($photo2Result['error'])) {
            flash('error', (string)$photo2Result['error']);
            $this->redirect('index.php?route=products/edit&id=' . $id);
        }

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
            'photo_1' => $photo1Result['path'] ?: ($product['photo_1'] ?? null),
            'photo_2' => $photo2Result['path'] ?: ($product['photo_2'] ?? null),
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
