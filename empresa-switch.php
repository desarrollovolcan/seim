<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

if (!isset($_SESSION['user']['id'])) {
    redirect('auth-2-sign-in.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    redirect('dashboard.php');
}

$empresaId = (int) ($_POST['empresa_id'] ?? 0);
if ($empresaId <= 0) {
    redirect('dashboard.php');
}

$empresasUsuario = load_user_empresas((int) $_SESSION['user']['id']);
if (!$empresasUsuario && is_superuser()) {
    $empresasUsuario = load_empresas();
}

$allowed = false;
foreach ($empresasUsuario as $empresa) {
    if ((int) ($empresa['id'] ?? 0) === $empresaId) {
        $allowed = true;
        break;
    }
}

if ($allowed) {
    $_SESSION['empresa_id'] = $empresaId;
    $_SESSION['user']['empresas'] = $empresasUsuario;
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
redirect($redirect);
