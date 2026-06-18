<?php

return [
    'driver' => env('IMAGE_DRIVER', 'auto'),

    'imgbb' => [
        'api_key' => env('IMAGEBB_API_KEY'),
        'endpoint' => env('IMAGEBB_API_URL', 'https://api.imgbb.com/1/upload'),
        'expiration' => env('IMAGEBB_EXPIRATION'),
    ],
];
