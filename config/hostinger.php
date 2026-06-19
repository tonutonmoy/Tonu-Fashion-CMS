<?php

return [
    /*
    | Hostinger shared hosting defaults (1GB RAM, no Redis/Supervisor).
    | Copy these into .env on production.
    */
    'cache_store' => env('CACHE_STORE', 'file'),
    'session_driver' => env('SESSION_DRIVER', 'file'),
    'queue_connection' => env('QUEUE_CONNECTION', 'database'),
    'image_driver' => env('IMAGE_DRIVER', 'local'),

    'cron' => [
        'schedule' => '* * * * * cd {{path}} && php artisan schedule:run >> /dev/null 2>&1',
        'warm_cache' => env('STOREFRONT_WARM_CRON', 'hourly'),
    ],

    'opcache' => [
        'recommended' => true,
        'validate_timestamps' => false,
    ],

    'build_assets_locally' => true,
];
