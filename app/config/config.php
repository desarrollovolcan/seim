<?php

return [
    'app' => [
        'name' => 'GoCreative Ges',
        'base_url' => '',
        'timezone' => 'America/Santiago',
    ],
    'db' => [
        'host' => 'localhost',
        'name' => 'gocreative_ges',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'security' => [
        'csrf_key' => 'csrf_token',
    ],
    'currency_format' => [
        'thousands_separator' => '.',
        'decimal_separator' => ',',
        'decimals' => 0,
        'symbol' => '$',
    ],
];
