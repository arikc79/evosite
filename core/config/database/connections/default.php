<?php
return [
    'driver' => env('DB_TYPE', 'mysql'),
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'evosite'),
    'username' => env('DB_USERNAME', 'root'), //$database_user
    'password' => env('DB_PASSWORD', 'root'), //$database_password
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'), // $database_connection_charset
    'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'), //$database_collation
    'prefix' => env('DB_PREFIX', 'e9f8_'),
    'strict' => env('DB_STRICT', false),
    'engine' => env('DB_ENGINE', 'innodb'),
    'options' => [
        PDO::ATTR_STRINGIFY_FETCHES => true,
    ]
];
