<?php
require __DIR__ . '/app/bootstrap.php';

$controller = new AuthController($config, $db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}
