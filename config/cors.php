<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:5173',
        'http://localhost:8080',
        'https://preview--foster-path.lovable.app',
        'https://fda2271d-029a-4223-a5fa-e8c5f3352bf9.lovableproject.com',
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.lovable\.app$#',
        '#^https://.*\.lovableproject\.com$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];