<?php

return [
    'driver' => env('IMAGE_DRIVER', 'local'),

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
