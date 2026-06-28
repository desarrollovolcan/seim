<?php
require __DIR__ . '/app/bootstrap.php';

$routes = require __DIR__ . '/app/routes.php';
$route = $_GET['route'] ?? 'dashboard';


if (!isset($routes[$route])) {
    http_response_code(404);
    echo 'Ruta no encontrada';
    exit;
}

if (Auth::check() && !can_access_route($db, $route, Auth::user())) {
    $user = Auth::user();
    $fallbackRoute = first_accessible_route($db, $routes, $user, $route);

    if ($fallbackRoute !== null) {
        $_SESSION['error'] = $route === 'dashboard'
            ? 'No tienes permisos para acceder al dashboard. Te redirigimos a la primera sección disponible.'
            : 'No tienes permisos para acceder a esta sección.';
        header('Location: index.php?route=' . urlencode($fallbackRoute));
        exit;
    }

    http_response_code(403);
    echo 'No tienes permisos para acceder a esta cuenta. Contacta al administrador.';
    exit;
}

[$controllerName, $method] = $routes[$route];
try {
    $controller = new $controllerName($config, $db);
    $controller->$method();
} catch (Throwable $e) {
    log_message('error', sprintf('Route %s failed: %s', $route, $e->getMessage()));
    http_response_code(500);
    include __DIR__ . '/error-500.php';
    exit;
}
