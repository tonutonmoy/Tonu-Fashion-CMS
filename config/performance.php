<?php

return [
    'cache_ttl' => (int) env('STOREFRONT_CACHE_TTL', 3600),

    'profiling' => (bool) env('PERFORMANCE_PROFILING', false),

    'sample_limit' => (int) env('PERFORMANCE_SAMPLE_LIMIT', 100),

    'slow_query_ms' => (int) env('PERFORMANCE_SLOW_QUERY_MS', 100),

    'homepage_initial_sections' => [
        'hero_slider',
        'categories',
        'featured_products',
        'new_arrivals',
        'flash_sale',
        'best_sellers',
        'customer_reviews',
        'blog',
        'faq',
        'newsletter',
    ],

    'homepage_lazy_sections' => [],

    'html_cache' => (bool) env('STOREFRONT_HTML_CACHE', true),
];
