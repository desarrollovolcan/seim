<?php
require __DIR__ . '/app/bootstrap.php';

$routes = require __DIR__ . '/app/routes.php';
$route = $_GET['route'] ?? 'dashboard';


function is_navigation_route(string $route): bool
{
    $blockedSuffixes = ['/store', '/update', '/delete', '/edit', '/create', '/show', '/message', '/messages', '/open', '/close', '/withdraw', '/download', '/print', '/export', '/process', '/bulk'];
    foreach ($blockedSuffixes as $suffix) {
        if (str_ends_with($route, $suffix)) {
            return false;
        }
    }

    return true;
}

if (!isset($routes[$route])) {
    http_response_code(404);
    echo 'Ruta no encontrada';
    exit;
}

if (Auth::check() && !can_access_route($db, $route, Auth::user())) {
    if ($route === 'dashboard') {
        http_response_code(403);
        echo 'No tienes permisos para acceder al dashboard.';
        exit;
    }

    $_SESSION['error'] = 'No tienes permisos para acceder a esta sección.';

    $user = Auth::user();
    $fallbackRoute = null;
    foreach (array_keys($routes) as $candidateRoute) {
        if ($candidateRoute === $route) {
            continue;
        }
        if (is_navigation_route($candidateRoute) && can_access_route($db, $candidateRoute, $user)) {
            $fallbackRoute = $candidateRoute;
            break;
        }
    }

    if ($fallbackRoute !== null) {
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
