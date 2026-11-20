<?php

return [
    'app' => [
        'nome' => 'Livraria Entrelinhas',
        'timezone' => 'America/Sao_Paulo',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3310',
        'name' => getenv('DB_NAME') ?: 'livraria',
        'user' => getenv('DB_USER') ?: 'livraria',
        'pass' => getenv('DB_PASS') ?: 'livraria123',
        'charset' => 'utf8mb4',
    ],
    'upload' => [
        'dir' => __DIR__ . '/../public/uploads/capas/',
        'max_bytes' => 2 * 1024 * 1024,
    ],
];
