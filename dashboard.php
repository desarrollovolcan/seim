<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/controllers/DashboardController.php';

require_permission('dashboard', 'view');

$controller = new DashboardController();
$data = $controller->show();
$metrics = $data['metrics'];
$recentSales = $data['recentSales'];
$lowStock = $data['lowStock'];
$profitMargins = $data['profitMargins'];
$ventasMensuales = $data['ventasMensuales'];

include __DIR__ . '/app/views/dashboard.php';
