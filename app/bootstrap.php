<?php

session_start();

$config = require __DIR__ . '/config/config.php';

require __DIR__ . '/helpers.php';
require __DIR__ . '/core/Database.php';
require __DIR__ . '/core/Model.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/core/Auth.php';
require __DIR__ . '/core/Mailer.php';
require __DIR__ . '/core/Validator.php';
require __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

try {
    $db = Database::getInstance($config['db']);
} catch (PDOException $e) {
    log_message('error', 'DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Error de conexión a la base de datos. Verifica la configuración.';
    exit;
}

$settings = new SettingsModel($db);
$currencyFormat = $settings->get('currency_format', []);
if (is_array($currencyFormat)) {
    $config['currency_format'] = array_merge($config['currency_format'] ?? [], $currencyFormat);
}

if (!isset($_SESSION['timezone_set'])) {
    date_default_timezone_set($config['app']['timezone']);
    $_SESSION['timezone_set'] = true;
}
