<?php

return [
    'driver' => env('IMAGE_DRIVER', 'auto'),

    'imgbb' => [
        'api_key' => env('IMAGEBB_API_KEY'),
        'api_url' => env('IMAGEBB_API_URL', 'https://api.imgbb.com/1/upload'),
    ],

    'directories' => [
        'products',
        'categories',
        'brands',
        'blogs',
        'hero',
        'pages',
    ],

    'variants' => [
        'thumb' => (int) env('IMAGE_THUMB_WIDTH', 400),
        'medium' => (int) env('IMAGE_MEDIUM_WIDTH', 800),
        'large' => (int) env('IMAGE_LARGE_WIDTH', 1200),
    ],

    'quality' => (int) env('IMAGE_QUALITY', 85),

    'prefer_webp' => (bool) env('IMAGE_PREFER_WEBP', true),
];
