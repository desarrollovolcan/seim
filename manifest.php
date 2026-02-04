<?php
require __DIR__ . '/app/bootstrap.php';

header('Content-Type: application/manifest+json; charset=utf-8');

try {
    $companySettings = login_company_settings($db);
} catch (Throwable $e) {
    log_message('error', 'Failed to load company settings for manifest: ' . $e->getMessage());
    $companySettings = [];
}

$appName = $companySettings['name'] ?? 'SEIM Inventario';
$shortName = $companySettings['name'] ?? 'SEIM';
$appIcon = 'pwa-icon.php?size=192';
$maskableIcon = 'pwa-icon.php?size=512&maskable=1';
$themeColor = '#6658dd';

$baseUrl = function_exists('base_url') ? rtrim(base_url(), '/') : '';
$startUrl = ($baseUrl !== '' ? $baseUrl : '') . '/index.php';
$scope = ($baseUrl !== '' ? $baseUrl : '') . '/';

$manifest = [
    'name' => $appName,
    'short_name' => $shortName,
    'description' => 'Sistema de gestión de inventario y operaciones comerciales.',
    'start_url' => $startUrl,
    'scope' => $scope,
    'display' => 'standalone',
    'orientation' => 'portrait',
    'background_color' => '#f7f9fb',
    'theme_color' => $themeColor,
    'lang' => 'es-CL',
    'icons' => [
        [
            'src' => $appIcon,
            'sizes' => '192x192',
            'type' => 'image/png',
        ],
        [
            'src' => $maskableIcon,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'maskable',
        ],
    ],
];

echo json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
