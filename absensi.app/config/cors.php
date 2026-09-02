<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost',
        'http://localhost:5173',
        'https://localhost:5173',
        'http://simkeuv2.uiidalwa.web.id',
        'https://simkeuv2.uiidalwa.web.id',
        'http://simkeuapp.uiidalwa.web.id',
        'https://simkeuapp.uiidalwa.web.id',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-Api-Key', 'X-Timestamp', 'X-Signature'],

    'max_age' => 0,

    'supports_credentials' => true,

];
