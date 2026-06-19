<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PerformanceMonitorService;
use App\Services\StorefrontCacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    public function __construct(
        private PerformanceMonitorService $monitor,
        private StorefrontCacheService $cache,
    ) {}

    public function index(): View
    {
        return view('admin.performance.index', [
            'summary' => $this->monitor->summary(),
            'samples' => $this->monitor->recentSamples(30),
            'routeAverages' => collect($this->monitor->routeAverages())
                ->sortByDesc('avg_ms')
                ->take(15),
            'cacheStats' => $this->cache->cacheStats(),
            'cacheTtl' => $this->cache->ttl(),
        ]);
    }

    public function warmCache(): RedirectResponse
    {
        Artisan::call('storefront:warm-cache');

        return back()->with('success', 'Storefront cache warmed successfully.');
    }
}
