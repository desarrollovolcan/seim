<?php

declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'gocreative_seim',
        'user' => getenv('DB_USER') ?: 'gocreative_seim',
        'pass' => getenv('DB_PASS') ?: '=c.(Q@,R]-+R8aU$',
        'charset' => 'utf8mb4',
    ],
];
