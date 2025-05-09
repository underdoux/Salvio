<?php
// Global configuration for Salvio POS system

return [
    'db' => [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/../database/salvio.sqlite',
        'prefix' => '',
    ],
    'app' => [
        'name' => 'Salvio',
        'base_url' => 'http://localhost', // Change as needed
        'debug' => true,
    ],
    'session' => [
        'name' => 'salvio_session',
        'cookie_lifetime' => 3600,
        'cookie_secure' => false, // Set true if using HTTPS
        'cookie_httponly' => true,
    ],
];
?>
