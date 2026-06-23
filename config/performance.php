<?php

use App\Services\ColorModeService;

return [
    'cache_ttl' => (int) env('STOREFRONT_CACHE_TTL', 7200),

    'profiling' => (bool) env('PERFORMANCE_PROFILING', false),

    'sample_limit' => (int) env('PERFORMANCE_SAMPLE_LIMIT', 100),

    'slow_query_ms' => (int) env('PERFORMANCE_SLOW_QUERY_MS', 100),

    /** Rendered on first paint (above the fold). */
    'homepage_initial_sections' => [
        'hero_slider',
        'categories',
        'featured_products',
        'new_arrivals',
    ],

    /** Loaded via AJAX after first paint. */
    'homepage_lazy_sections' => [
        'flash_sale',
        'best_sellers',
        'customer_reviews',
        'blog',
        'faq',
        'newsletter',
    ],

    'html_cache' => (bool) env('STOREFRONT_HTML_CACHE', true),

    /** Skip Google Fonts — faster first paint (uses system-ui). */
    'use_system_fonts' => (bool) env('STOREFRONT_SYSTEM_FONTS', true),

    /** Re-warm homepage/shop/product HTML cache on this schedule. */
    'warm_cache_cron' => env('STOREFRONT_WARM_CRON', 'hourly'),

    'color_modes' => ['light', 'dark'],
];
