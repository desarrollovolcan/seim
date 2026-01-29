<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/controllers/DashboardController.php';

require_permission('dashboard', 'view');

$controller = new DashboardController();
$data = $controller->show();
$resumen = $data['resumen'];
$lowStock = $data['lowStock'];
$ventasProductos = $data['ventasProductos'];
$gananciaAcumulada = $data['gananciaAcumulada'];

include __DIR__ . '/app/views/dashboard.php';
