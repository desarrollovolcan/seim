<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/controllers/DashboardController.php';

$controller = new DashboardController();
$data = $controller->show();
$metrics = $data['metrics'];
$recentSales = $data['recentSales'];

include __DIR__ . '/app/views/dashboard.php';
