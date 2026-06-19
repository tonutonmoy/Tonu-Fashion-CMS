import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/storefront-header-mobile.css',
                'resources/js/storefront.js',
                'resources/js/checkout.js',
                'resources/js/admin-entry.js',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios'],
                    turbo: ['@hotwired/turbo'],
                },
            },
        },
        cssMinify: true,
        minify: 'esbuild',
    },
});
