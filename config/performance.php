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

    /** Skip Google Fonts — faster first paint (uses system-ui). */
    'use_system_fonts' => (bool) env('STOREFRONT_SYSTEM_FONTS', true),

    /** Re-warm homepage/shop/product HTML cache on this schedule. */
    'warm_cache_cron' => env('STOREFRONT_WARM_CRON', 'hourly'),
];
