<?php

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('CSRF token inválido.');
    }
}

function e(mixed $value): string
{
    if ($value === null) {
        return '';
    }

    if (is_bool($value)) {
        $value = $value ? '1' : '0';
    }

    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function mb_substr_safe(string $value, int $start, ?int $length = null): string
{
    if (function_exists('mb_substr')) {
        return $length === null ? mb_substr($value, $start) : mb_substr($value, $start, $length);
    }

    return $length === null ? substr($value, $start) : substr($value, $start, $length);
}

function generate_three_letter_code(string $name): string
{
    $value = trim($name);
    if ($value === '') {
        return '';
    }

    $normalized = $value;
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        if ($converted !== false) {
            $normalized = $converted;
        }
    }

    $normalized = strtoupper($normalized);
    $normalized = preg_replace('/[^A-Z]/', '', $normalized) ?? '';

    if ($normalized === '') {
        $normalized = strtoupper(preg_replace('/\\s+/', '', $value) ?? $value);
    }

    return mb_substr_safe($normalized, 0, 3);
}

function app_config(?string $key = null, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        if (isset($GLOBALS['config']) && is_array($GLOBALS['config'])) {
            $config = $GLOBALS['config'];
        } else {
            $config = require __DIR__ . '/config/config.php';
        }
    }

    if ($key === null) {
        return $config;
    }

    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function base_url(): string
{
    $configured = trim((string)app_config('app.base_url', ''));
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host === '') {
        return '';
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

    return $scheme . '://' . $host . $basePath;
}

function currency_format_settings(): array
{
    return app_config('currency_format', [
        'thousands_separator' => '.',
        'decimal_separator' => ',',
        'decimals' => 0,
        'symbol' => '$',
    ]);
}

function format_currency(float $amount, ?int $decimals = null): string
{
    $settings = currency_format_settings();
    $precision = $decimals ?? (int)($settings['decimals'] ?? 0);
    $decimalSeparator = (string)($settings['decimal_separator'] ?? ',');
    $thousandsSeparator = (string)($settings['thousands_separator'] ?? '.');
    $symbol = (string)($settings['symbol'] ?? '$');

    return $symbol . number_format($amount, $precision, $decimalSeparator, $thousandsSeparator);
}

function format_date(?string $date, string $format = 'd/m/Y'): string
{
    if (empty($date)) {
        return '';
    }

    $dateTime = DateTime::createFromFormat('Y-m-d', $date)
        ?: DateTime::createFromFormat('Y-m-d H:i:s', $date);

    if (!$dateTime) {
        try {
            $dateTime = new DateTime($date);
        } catch (Exception $e) {
            return (string)$date;
        }
    }

    return $dateTime->format($format);
}

function render_id_badge(null|int|string $id, string $label = 'ID'): string
{
    if ($id === null || $id === '') {
        return '<span class="badge bg-light text-muted">ID no disponible</span>';
    }

    $value = is_numeric($id) ? (int)$id : (string)$id;
    $idText = '#' . e((string)$value);

    return sprintf(
        '<span class="badge bg-light text-body fw-semibold" title="Identificador del registro">%s %s</span>',
        e($label),
        $idText
    );
}

function log_message(string $level, string $message): void
{
    $logFile = __DIR__ . '/../storage/logs/app.log';
    $entry = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message);
    file_put_contents($logFile, $entry, FILE_APPEND);
}

function current_company_id(): ?int
{
    $companyId = null;
    if (class_exists('Auth')) {
        $user = Auth::user();
        if (!empty($user['company_id'])) {
            $companyId = (int)$user['company_id'];
        }
    }
    if (!$companyId && !empty($_SESSION['client_company_id'])) {
        $companyId = (int)$_SESSION['client_company_id'];
    }

    return $companyId ?: null;
}

function user_company_ids(Database $db, ?array $user): array
{
    if (!$user) {
        return [];
    }
    $companyIds = [];
    if (!empty($user['company_id'])) {
        $companyIds[] = (int)$user['company_id'];
    }
    $rows = $db->fetchAll(
        'SELECT company_id FROM user_companies WHERE user_id = :user_id',
        ['user_id' => (int)($user['id'] ?? 0)]
    );
    foreach ($rows as $row) {
        $companyIds[] = (int)$row['company_id'];
    }
    $companyIds = array_values(array_unique(array_filter($companyIds)));
    sort($companyIds);
    return $companyIds;
}

function ensure_upload_directory(string $directory): ?string
{
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        return 'No pudimos crear la carpeta de cargas en el servidor.';
    }

    if (!is_writable($directory)) {
        @chmod($directory, 0775);
    }

    if (!is_writable($directory)) {
        return 'No hay permisos de escritura para guardar archivos en el servidor.';
    }

    return null;
}

function login_company_settings(Database $db): array
{
    $settingsModel = new SettingsModel($db);
    $companySettings = $settingsModel->get('company', []);
    if (!empty($companySettings['login_logo'] ?? '')) {
        return $companySettings;
    }
    $firstCompany = $db->fetch('SELECT id FROM companies ORDER BY id ASC LIMIT 1');
    if ($firstCompany) {
        $fallbackSettings = $settingsModel->get('company', [], (int)$firstCompany['id']);
        if (!empty($fallbackSettings)) {
            $companySettings = array_merge($companySettings, $fallbackSettings);
        }
    }
    return $companySettings;
}

function login_logo_src(array $companySettings): string
{
    $logoColor = $companySettings['logo_color'] ?? 'assets/images/logo.png';
    $logoBlack = $companySettings['logo_black'] ?? 'assets/images/logo-black.png';
    $loginLogoVariant = $companySettings['login_logo_variant'] ?? 'light';
    $loginLogo = $companySettings['login_logo'] ?? '';
    if ($loginLogo !== '') {
        return $loginLogo;
    }
    return $loginLogoVariant === 'dark' ? $logoBlack : $logoColor;
}


function upload_avatar(?array $file, string $prefix): array
{
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => 'No pudimos cargar la imagen, intenta nuevamente.'];
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return ['path' => null, 'error' => 'La imagen supera el tamaño máximo de 2MB.'];
    }

    $info = getimagesize($file['tmp_name'] ?? '');
    if ($info === false || empty($info['mime'])) {
        return ['path' => null, 'error' => 'El archivo seleccionado no es una imagen válida.'];
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $extension = $allowed[$info['mime']] ?? null;
    if ($extension === null) {
        return ['path' => null, 'error' => 'Solo se permiten imágenes JPG, PNG o WEBP.'];
    }

    $directory = __DIR__ . '/../storage/uploads/avatars';
    $directoryError = ensure_upload_directory($directory);
    if ($directoryError !== null) {
        return ['path' => null, 'error' => $directoryError];
    }

    $filename = sprintf('%s-%s.%s', $prefix, bin2hex(random_bytes(8)), $extension);
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['path' => null, 'error' => 'No pudimos guardar la imagen en el servidor.'];
    }

    return ['path' => 'storage/uploads/avatars/' . $filename, 'error' => null];
}

function normalize_rut(?string $rut): string
{
    $rut = strtoupper((string)$rut);
    $rut = preg_replace('/[^0-9K]/', '', $rut ?? '');
    if ($rut === '' || strlen($rut) < 2) {
        return '';
    }
    $body = substr($rut, 0, -1);
    $dv = substr($rut, -1);
    return $body . '-' . $dv;
}

function is_valid_rut(string $rut): bool
{
    $rut = strtoupper(preg_replace('/[^0-9K]/', '', $rut));
    if ($rut === '' || strlen($rut) < 2) {
        return false;
    }
    $body = substr($rut, 0, -1);
    $dv = substr($rut, -1);
    $sum = 0;
    $multiplier = 2;
    for ($i = strlen($body) - 1; $i >= 0; $i--) {
        $sum += (int)$body[$i] * $multiplier;
        $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
    }
    $remainder = 11 - ($sum % 11);
    $expected = match ($remainder) {
        11 => '0',
        10 => 'K',
        default => (string)$remainder,
    };
    return $dv === $expected;
}

function sii_document_types(): array
{
    return [
        'factura_electronica' => 'Factura electrónica',
        'factura_exenta' => 'Factura exenta',
        'boleta_electronica' => 'Boleta electrónica',
        'nota_credito' => 'Nota de crédito',
        'nota_debito' => 'Nota de débito',
        'guia_despacho' => 'Guía de despacho',
        'otro' => 'Otro documento',
    ];
}

function sii_receiver_payload(array $entity): array
{
    return [
        'sii_receiver_rut' => normalize_rut($entity['rut'] ?? $entity['tax_id'] ?? ''),
        'sii_receiver_name' => trim((string)($entity['name'] ?? '')),
        'sii_receiver_giro' => trim((string)($entity['giro'] ?? '')),
        'sii_receiver_address' => trim((string)($entity['address'] ?? '')),
        'sii_receiver_commune' => trim((string)($entity['commune'] ?? '')),
    ];
}

function sii_document_payload(array $source, array $fallback = []): array
{
    $payload = [
        'sii_document_type' => trim((string)($source['sii_document_type'] ?? '')),
        'sii_document_number' => trim((string)($source['sii_document_number'] ?? '')),
        'sii_receiver_rut' => normalize_rut($source['sii_receiver_rut'] ?? ''),
        'sii_receiver_name' => trim((string)($source['sii_receiver_name'] ?? '')),
        'sii_receiver_giro' => trim((string)($source['sii_receiver_giro'] ?? '')),
        'sii_receiver_address' => trim((string)($source['sii_receiver_address'] ?? '')),
        'sii_receiver_commune' => trim((string)($source['sii_receiver_commune'] ?? '')),
        'sii_tax_rate' => (float)($source['sii_tax_rate'] ?? 19),
        'sii_exempt_amount' => (float)($source['sii_exempt_amount'] ?? 0),
    ];
    foreach ($fallback as $key => $value) {
        if (!array_key_exists($key, $payload)) {
            continue;
        }
        if (is_string($payload[$key]) && trim($payload[$key]) === '' && trim((string)$value) !== '') {
            $payload[$key] = $value;
        }
    }
    return $payload;
}

function sii_required_fields(): array
{
    return [
        'sii_document_type' => 'el tipo de documento',
        'sii_document_number' => 'el folio del documento',
        'sii_receiver_rut' => 'el RUT del receptor',
        'sii_receiver_name' => 'la razón social del receptor',
        'sii_receiver_giro' => 'el giro del receptor',
        'sii_receiver_address' => 'la dirección del receptor',
        'sii_receiver_commune' => 'la comuna del receptor',
    ];
}

function validate_sii_document_payload(array $payload, ?array $requiredFields = null): array
{
    $requiredFields = $requiredFields ?? sii_required_fields();
    $errors = [];
    foreach ($requiredFields as $field => $label) {
        if (trim((string)($payload[$field] ?? '')) === '') {
            $errors[] = 'Completa ' . $label . '.';
        }
    }
    $documentTypes = sii_document_types();
    if (!empty($payload['sii_document_type']) && !array_key_exists($payload['sii_document_type'], $documentTypes)) {
        $errors[] = 'Selecciona un tipo de documento válido.';
    }
    if (!empty($payload['sii_receiver_rut']) && !is_valid_rut($payload['sii_receiver_rut'])) {
        $errors[] = 'El RUT del receptor no es válido.';
    }
    if (($payload['sii_tax_rate'] ?? 0) < 0 || ($payload['sii_tax_rate'] ?? 0) > 100) {
        $errors[] = 'La tasa de impuesto debe estar entre 0 y 100.';
    }
    if (($payload['sii_exempt_amount'] ?? 0) < 0) {
        $errors[] = 'El monto exento no puede ser negativo.';
    }
    return $errors;
}

function upload_company_logo(?array $file, string $prefix): array
{
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => 'No pudimos cargar la imagen, intenta nuevamente.'];
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return ['path' => null, 'error' => 'La imagen supera el tamaño máximo de 2MB.'];
    }

    $info = getimagesize($file['tmp_name'] ?? '');
    if ($info === false || empty($info['mime'])) {
        return ['path' => null, 'error' => 'El archivo seleccionado no es una imagen válida.'];
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $extension = $allowed[$info['mime']] ?? null;
    if ($extension === null) {
        return ['path' => null, 'error' => 'Solo se permiten imágenes JPG, PNG o WEBP.'];
    }

    $directory = __DIR__ . '/../storage/uploads/logos';
    $directoryError = ensure_upload_directory($directory);
    if ($directoryError !== null) {
        return ['path' => null, 'error' => $directoryError];
    }

    $filename = sprintf('%s-%s.%s', $prefix, bin2hex(random_bytes(8)), $extension);
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['path' => null, 'error' => 'No pudimos guardar la imagen en el servidor.'];
    }

    return ['path' => 'storage/uploads/logos/' . $filename, 'error' => null];
}

function audit(Database $db, int $userId, string $action, string $entity, ?int $entityId = null): void
{
    $companyId = current_company_id();
    try {
        $db->execute(
            'INSERT INTO audit_logs (company_id, user_id, action, entity, entity_id, created_at) VALUES (:company_id, :user_id, :action, :entity, :entity_id, NOW())',
            [
                'company_id' => $companyId,
                'user_id' => $userId,
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
            ]
        );
    } catch (Throwable $e) {
        log_message('error', sprintf('Failed to write audit log for %s: %s', $entity, $e->getMessage()));
    }
}

function render_template_vars(string $html, array $context = []): string
{
    $defaults = [
        'cliente_nombre' => $context['cliente_nombre'] ?? '',
        'rut' => $context['rut'] ?? '',
        'monto_total' => $context['monto_total'] ?? '',
        'fecha_vencimiento' => $context['fecha_vencimiento'] ?? '',
        'servicio_nombre' => $context['servicio_nombre'] ?? '',
        'dominio' => $context['dominio'] ?? '',
        'hosting' => $context['hosting'] ?? '',
        'fecha_eliminacion' => $context['fecha_eliminacion'] ?? '',
        'link_pago' => $context['link_pago'] ?? '',
        'numero_factura' => $context['numero_factura'] ?? '',
        'detalle_factura' => $context['detalle_factura'] ?? '',
        'monto_pagado' => $context['monto_pagado'] ?? '',
        'saldo_pendiente' => $context['saldo_pendiente'] ?? '',
        'fecha_pago' => $context['fecha_pago'] ?? '',
        'metodo_pago' => $context['metodo_pago'] ?? '',
        'referencia_pago' => $context['referencia_pago'] ?? '',
    ];

    foreach ($defaults as $key => $value) {
        $html = str_replace('{{' . $key . '}}', (string)$value, $html);
    }

    return $html;
}

function build_flow_signature(array $params, string $secretKey): string
{
    ksort($params);
    $toSign = '';
    foreach ($params as $key => $value) {
        $toSign .= $key . $value;
    }
    return hash_hmac('sha256', $toSign, $secretKey);
}

function create_flow_payment_link(array $config, array $payload): ?string
{
    $apiKey = trim((string)($config['api_key'] ?? ''));
    $secretKey = trim((string)($config['secret_key'] ?? ''));
    $baseUrl = trim((string)($config['base_url'] ?? ''));
    $returnUrl = trim((string)($config['return_url'] ?? ''));
    $confirmationUrl = trim((string)($config['confirmation_url'] ?? ''));

    if ($apiKey === '' || $secretKey === '' || $baseUrl === '' || $returnUrl === '' || $confirmationUrl === '') {
        return null;
    }

    $params = [
        'apiKey' => $apiKey,
        'commerceOrder' => (string)($payload['commerce_order'] ?? ''),
        'subject' => (string)($payload['subject'] ?? ''),
        'currency' => (string)($payload['currency'] ?? 'CLP'),
        'amount' => (string)($payload['amount'] ?? ''),
        'email' => (string)($payload['email'] ?? ''),
        'urlReturn' => $returnUrl,
        'urlConfirmation' => $confirmationUrl,
    ];

    if ($params['commerceOrder'] === '' || $params['subject'] === '' || $params['amount'] === '' || $params['email'] === '') {
        return null;
    }

    $params['s'] = build_flow_signature($params, $secretKey);

    $trimmedBase = rtrim($baseUrl, '/');
    if (!str_ends_with($trimmedBase, '/api')) {
        $trimmedBase .= '/api';
    }
    $endpoint = $trimmedBase . '/payment/create';
    $response = null;
    $curl = curl_init($endpoint);
    if ($curl === false) {
        return null;
    }
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($curl);
    if ($response === false) {
        log_message('error', 'Flow payment error: ' . curl_error($curl));
        curl_close($curl);
        return null;
    }
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($status < 200 || $status >= 300) {
        log_message('error', 'Flow payment error: HTTP ' . $status . ' - ' . $response);
        return null;
    }

    $data = json_decode($response, true);
    if (!is_array($data) || empty($data['url']) || empty($data['token'])) {
        log_message('error', 'Flow payment error: respuesta inválida ' . $response);
        return null;
    }

    return $data['url'] . '?token=' . $data['token'];
}

function permission_catalog(): array
{
    return [
        'dashboard' => [
            'label' => 'Dashboard',
            'routes' => ['dashboard'],
            'legacy_key' => 'dashboard',
            'view_key' => 'dashboard_view',
            'edit_key' => null,
        ],
        'crm' => [
            'label' => 'CRM Comercial',
            'routes' => ['crm'],
            'legacy_key' => 'crm',
            'view_key' => 'crm_view',
            'edit_key' => null,
        ],
        'clients' => [
            'label' => 'Clientes',
            'routes' => ['clients'],
            'legacy_key' => 'clients',
            'view_key' => 'clients_view',
            'edit_key' => 'clients_edit',
        ],
        'tickets' => [
            'label' => 'Tickets de soporte',
            'routes' => ['tickets'],
            'legacy_key' => 'tickets',
            'view_key' => 'tickets_view',
            'edit_key' => 'tickets_edit',
        ],
        'projects' => [
            'label' => 'Proyectos',
            'routes' => ['projects'],
            'legacy_key' => 'projects',
            'view_key' => 'projects_view',
            'edit_key' => 'projects_edit',
        ],
        'documents' => [
            'label' => 'Documentos',
            'routes' => ['documents'],
            'legacy_key' => 'documents',
            'view_key' => 'documents_view',
            'edit_key' => 'documents_edit',
        ],
        'products' => [
            'label' => 'Productos',
            'routes' => ['products'],
            'legacy_key' => 'products',
            'view_key' => 'products_view',
            'edit_key' => 'products_edit',
        ],
        'produced_products' => [
            'label' => 'Productos fabricados',
            'routes' => ['produced-products', 'produced_products'],
            'legacy_key' => 'produced_products',
            'view_key' => 'produced_products_view',
            'edit_key' => 'produced_products_edit',
        ],
        'suppliers' => [
            'label' => 'Proveedores',
            'routes' => ['suppliers'],
            'legacy_key' => 'suppliers',
            'view_key' => 'suppliers_view',
            'edit_key' => 'suppliers_edit',
        ],
        'purchases' => [
            'label' => 'Compras de productos',
            'routes' => ['purchases'],
            'legacy_key' => 'purchases',
            'view_key' => 'purchases_view',
            'edit_key' => 'purchases_edit',
        ],
        'purchase_orders' => [
            'label' => 'Órdenes de compra',
            'routes' => ['purchase-orders'],
            'legacy_key' => 'purchases',
            'view_key' => 'purchase_orders_view',
            'edit_key' => 'purchase_orders_edit',
        ],
        'production' => [
            'label' => 'Producción',
            'routes' => ['production'],
            'legacy_key' => 'inventory',
            'view_key' => 'production_view',
            'edit_key' => 'production_edit',
        ],
        'sales' => [
            'label' => 'Ventas y POS',
            'routes' => ['sales', 'pos'],
            'legacy_key' => 'sales',
            'view_key' => 'sales_view',
            'edit_key' => 'sales_edit',
        ],
        'product_families' => [
            'label' => 'Familias de producto',
            'routes' => ['maintainers/product-families'],
            'legacy_key' => 'product_families',
            'view_key' => 'product_families_view',
            'edit_key' => 'product_families_edit',
        ],
        'product_subfamilies' => [
            'label' => 'Subfamilias de producto',
            'routes' => ['maintainers/product-subfamilies'],
            'legacy_key' => 'product_subfamilies',
            'view_key' => 'product_subfamilies_view',
            'edit_key' => 'product_subfamilies_edit',
        ],
        'competitor_companies' => [
            'label' => 'Empresas competencia',
            'routes' => ['maintainers/competitor-companies'],
            'legacy_key' => 'competitor_companies',
            'view_key' => 'competitor_companies_view',
            'edit_key' => 'competitor_companies_edit',
        ],
        'services' => [
            'label' => 'Servicios (clientes)',
            'routes' => ['services'],
            'legacy_key' => 'services',
            'view_key' => 'services_view',
            'edit_key' => 'services_edit',
        ],
        'system_services' => [
            'label' => 'Servicios catálogo',
            'routes' => ['maintainers/services'],
            'legacy_key' => 'maintainers',
            'view_key' => 'system_services_view',
            'edit_key' => 'system_services_edit',
        ],
        'service_types' => [
            'label' => 'Tipos de servicios',
            'routes' => ['maintainers/service-types'],
            'legacy_key' => 'maintainers',
            'view_key' => 'service_types_view',
            'edit_key' => 'service_types_edit',
        ],
        'chile_regions' => [
            'label' => 'Regiones',
            'routes' => ['maintainers/chile-regions'],
            'legacy_key' => 'maintainers',
            'view_key' => 'chile_regions_view',
            'edit_key' => 'chile_regions_edit',
        ],
        'quotes' => [
            'label' => 'Cotizaciones',
            'routes' => ['quotes'],
            'legacy_key' => 'quotes',
            'view_key' => 'quotes_view',
            'edit_key' => 'quotes_edit',
        ],
        'invoices' => [
            'label' => 'Facturas',
            'routes' => ['invoices'],
            'legacy_key' => 'invoices',
            'view_key' => 'invoices_view',
            'edit_key' => 'invoices_edit',
        ],
        'payments' => [
            'label' => 'Pagos',
            'routes' => ['payments'],
            'legacy_key' => 'invoices',
            'view_key' => 'payments_view',
            'edit_key' => null,
        ],
        'hr_employees' => [
            'label' => 'Trabajadores',
            'routes' => ['hr/employees'],
            'legacy_key' => 'hr',
            'view_key' => 'hr_employees_view',
            'edit_key' => 'hr_employees_edit',
        ],
        'hr_contracts' => [
            'label' => 'Contratos',
            'routes' => ['hr/contracts'],
            'legacy_key' => 'hr',
            'view_key' => 'hr_contracts_view',
            'edit_key' => 'hr_contracts_edit',
        ],
        'hr_attendance' => [
            'label' => 'Asistencia',
            'routes' => ['hr/attendance', 'hr/clock'],
            'legacy_key' => 'hr',
            'view_key' => 'hr_attendance_view',
            'edit_key' => 'hr_attendance_edit',
        ],
        'hr_payrolls' => [
            'label' => 'Remuneraciones',
            'routes' => ['hr/payrolls'],
            'legacy_key' => 'hr',
            'view_key' => 'hr_payrolls_view',
            'edit_key' => 'hr_payrolls_edit',
        ],
        'hr_maintainers' => [
            'label' => 'Mantenedores RRHH',
            'routes' => [
                'maintainers/hr-departments',
                'maintainers/hr-positions',
                'maintainers/hr-contract-types',
                'maintainers/hr-work-schedules',
                'maintainers/hr-payroll-items',
                'maintainers/hr-health-providers',
                'maintainers/hr-pension-funds',
            ],
            'legacy_key' => 'maintainers',
            'view_key' => 'hr_maintainers_view',
            'edit_key' => 'hr_maintainers_edit',
        ],
        'email_templates' => [
            'label' => 'Plantillas Email',
            'routes' => ['email-templates'],
            'legacy_key' => 'email_templates',
            'view_key' => 'email_templates_view',
            'edit_key' => 'email_templates_edit',
        ],
        'email_queue' => [
            'label' => 'Cola de Correos',
            'routes' => ['email-queue'],
            'legacy_key' => 'email_queue',
            'view_key' => 'email_queue_view',
            'edit_key' => 'email_queue_edit',
        ],
        'settings' => [
            'label' => 'Configuraciones',
            'routes' => ['settings'],
            'legacy_key' => 'settings',
            'view_key' => 'settings_view',
            'edit_key' => 'settings_edit',
        ],
        'email_config' => [
            'label' => 'Configuración de correo',
            'routes' => ['maintainers/email-config'],
            'legacy_key' => 'maintainers',
            'view_key' => 'email_config_view',
            'edit_key' => 'email_config_edit',
        ],
        'online_payments_config' => [
            'label' => 'Pagos en línea',
            'routes' => ['maintainers/online-payments'],
            'legacy_key' => 'maintainers',
            'view_key' => 'online_payments_config_view',
            'edit_key' => 'online_payments_config_edit',
        ],
        'accounting' => [
            'label' => 'Contabilidad general',
            'routes' => ['accounting'],
            'legacy_key' => 'accounting',
            'view_key' => 'accounting_view',
            'edit_key' => 'accounting_edit',
        ],
        'taxes' => [
            'label' => 'Impuestos',
            'routes' => ['taxes'],
            'legacy_key' => 'taxes',
            'view_key' => 'taxes_view',
            'edit_key' => 'taxes_edit',
        ],
        'honorarios' => [
            'label' => 'Honorarios',
            'routes' => ['honorarios'],
            'legacy_key' => 'honorarios',
            'view_key' => 'honorarios_view',
            'edit_key' => 'honorarios_edit',
        ],
        'fixed_assets' => [
            'label' => 'Activos fijos',
            'routes' => ['fixed-assets'],
            'legacy_key' => 'fixed_assets',
            'view_key' => 'fixed_assets_view',
            'edit_key' => 'fixed_assets_edit',
        ],
        'treasury' => [
            'label' => 'Tesorería y bancos',
            'routes' => ['treasury'],
            'legacy_key' => 'treasury',
            'view_key' => 'treasury_view',
            'edit_key' => 'treasury_edit',
        ],
        'inventory' => [
            'label' => 'Inventario (kardex)',
            'routes' => ['inventory'],
            'legacy_key' => 'inventory',
            'view_key' => 'inventory_view',
            'edit_key' => 'inventory_edit',
        ],
        'companies' => [
            'label' => 'Empresas',
            'routes' => ['companies'],
            'legacy_key' => 'companies',
            'view_key' => 'companies_view',
            'edit_key' => 'companies_edit',
        ],
        'users' => [
            'label' => 'Usuarios',
            'routes' => ['users'],
            'legacy_key' => 'users',
            'view_key' => 'users_view',
            'edit_key' => 'users_edit',
        ],
        'roles' => [
            'label' => 'Roles de usuarios',
            'routes' => ['roles'],
            'legacy_key' => 'roles',
            'view_key' => 'roles_view',
            'edit_key' => 'roles_edit',
        ],
        'users_companies' => [
            'label' => 'Asignar empresa',
            'routes' => ['users/assign-company'],
            'legacy_key' => 'users_companies',
            'view_key' => 'users_companies_view',
            'edit_key' => 'users_companies_edit',
        ],
        'users_permissions' => [
            'label' => 'Permisos de usuarios',
            'routes' => ['users/permissions'],
            'legacy_key' => 'users_permissions',
            'view_key' => 'users_permissions_view',
            'edit_key' => 'users_permissions_edit',
        ],
        'calendar' => [
            'label' => 'Calendario',
            'routes' => ['calendar'],
            'legacy_key' => 'calendar',
            'view_key' => 'calendar_view',
            'edit_key' => 'calendar_edit',
        ],
        'company_switch' => [
            'label' => 'Cambio de empresa',
            'routes' => ['auth/switch-company'],
            'legacy_key' => 'company_switch',
            'view_key' => 'company_switch_view',
            'edit_key' => 'company_switch_edit',
        ],
    ];
}

function flash(string $type, string $message): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    if (!isset($_SESSION['flash'][$type])) {
        $_SESSION['flash'][$type] = [];
    }
    $_SESSION['flash'][$type][] = $message;
}

function consume_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function permission_key_for_route(string $route): ?string
{
    $catalog = permission_catalog();
    foreach ($catalog as $key => $data) {
        foreach ($data['routes'] as $prefix) {
            if ($route === $prefix || str_starts_with($route, $prefix . '/')) {
                $isEdit = permission_is_edit_route($route);
                if ($isEdit && !empty($data['edit_key'])) {
                    return $data['edit_key'];
                }
                return $data['view_key'] ?? $data['edit_key'] ?? $key;
            }
        }
    }
    return null;
}

function permission_is_edit_route(string $route): bool
{
    $editMarkers = [
        '/create',
        '/store',
        '/edit',
        '/update',
        '/delete',
        '/bulk',
        '/bulk-store',
        '/generate',
        '/send',
        '/test',
        '/import',
        '/export',
    ];
    foreach ($editMarkers as $marker) {
        if (str_contains($route, $marker)) {
            return true;
        }
    }
    return false;
}

function permission_legacy_key_for(string $key): ?string
{
    foreach (permission_catalog() as $data) {
        if (($data['view_key'] ?? null) === $key || ($data['edit_key'] ?? null) === $key) {
            return $data['legacy_key'] ?? null;
        }
    }
    return null;
}

function permission_edit_key_for_view(string $viewKey): ?string
{
    foreach (permission_catalog() as $data) {
        if (($data['view_key'] ?? null) === $viewKey) {
            return $data['edit_key'] ?? null;
        }
    }
    return null;
}

function role_permissions(Database $db, int $roleId): array
{
    static $cache = [];
    if (isset($cache[$roleId])) {
        return $cache[$roleId];
    }
    $rows = $db->fetchAll('SELECT permission_key FROM role_permissions WHERE role_id = :role_id', [
        'role_id' => $roleId,
    ]);
    $permissions = array_map(static fn(array $row) => $row['permission_key'], $rows);
    $cache[$roleId] = $permissions;
    return $permissions;
}

function can_access_route(Database $db, string $route, ?array $user): bool
{
    if (!$user) {
        return false;
    }
    if (($user['role'] ?? '') === 'admin') {
        return true;
    }
    $key = permission_key_for_route($route);
    if ($key === null) {
        return true;
    }
    $roleId = (int)($user['role_id'] ?? 0);
    if ($roleId === 0 && !empty($user['role'])) {
        $roleRow = $db->fetch('SELECT id FROM roles WHERE name = :name', ['name' => $user['role']]);
        $roleId = (int)($roleRow['id'] ?? 0);
    }
    if ($roleId === 0) {
        return false;
    }
    $permissions = role_permissions($db, $roleId);
    if (in_array($key, $permissions, true)) {
        return true;
    }
    $editKey = permission_edit_key_for_view($key);
    if ($editKey && in_array($editKey, $permissions, true)) {
        return true;
    }
    $legacyKey = permission_legacy_key_for($key);
    return $legacyKey ? in_array($legacyKey, $permissions, true) : false;
}

function create_notification(Database $db, ?int $companyId, string $title, string $message, string $type = 'info'): void
{
    if (!$companyId) {
        return;
    }
    $db->execute(
        'INSERT INTO notifications (company_id, title, message, type, created_at, updated_at)
         VALUES (:company_id, :title, :message, :type, NOW(), NOW())',
        [
            'company_id' => $companyId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]
    );
}
