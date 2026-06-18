<?php

return [
    'currency_symbol' => env('CURRENCY_SYMBOL', '৳'),
    'currency_code' => env('CURRENCY_CODE', 'BDT'),
    'shipping' => [
        'inside_dhaka' => (float) env('SHIPPING_INSIDE_DHAKA', 80),
        'outside_dhaka' => (float) env('SHIPPING_OUTSIDE_DHAKA', 150),
        'free_shipping_limit' => (float) env('FREE_SHIPPING_LIMIT', 2000),
    ],
    'shipping_flat_rate' => (float) env('SHIPPING_FLAT_RATE', 120),
    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
    'colors' => ['Black', 'White', 'Navy', 'Maroon', 'Red', 'Blue', 'Green', 'Gray', 'Beige', 'Pink'],
    'image' => [
        'product_max_width' => 1200,
        'large_width' => 1200,
        'medium_width' => 800,
        'thumbnail_width' => 400,
        'product_quality' => 85,
        'prefer_webp' => true,
    ],
    'pagination' => [
        'products' => 12,
        'admin' => 10,
        'orders' => 10,
    ],
];
