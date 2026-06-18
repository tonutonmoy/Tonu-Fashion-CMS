<?php

return [
    'skip_local' => env('LICENSE_SKIP_LOCAL', true),
    'server_url' => env('LICENSE_SERVER_URL'),
    'secret' => env('LICENSE_SECRET'),
    'cache_ttl' => (int) env('LICENSE_CACHE_TTL', 86400),
    'provider_name' => env('LICENSE_PROVIDER_NAME', 'Fashion BD'),
    'provider_email' => env('LICENSE_PROVIDER_EMAIL', 'support@fashionbd.com'),
    'provider_url' => env('LICENSE_PROVIDER_URL', 'https://fashionbd.com'),
];
