<?php
$job = $argv[1] ?? '';

$map = [
    'check_expirations' => __DIR__ . '/jobs/check_expirations.php',
    'generate_invoices' => __DIR__ . '/jobs/generate_invoices.php',
    'send_scheduled_emails' => __DIR__ . '/jobs/send_scheduled_emails.php',
];

if (!isset($map[$job])) {
    echo "Job inválido\n";
    exit(1);
}

require $map[$job];
