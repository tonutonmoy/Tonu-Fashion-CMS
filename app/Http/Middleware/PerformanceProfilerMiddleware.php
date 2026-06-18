<?php

namespace App\Http\Middleware;

use App\Services\PerformanceMonitorService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PerformanceProfilerMiddleware
{
    private float $requestStartedAt = 0;

    public function __construct(private PerformanceMonitorService $monitor) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('performance.profiling') || $request->is('admin/performance/*')) {
            return $next($request);
        }

        $this->requestStartedAt = microtime(true);
        $this->monitor->start();

        DB::listen(function ($query) {
            $this->monitor->recordQuery($query->sql, (float) $query->time);
        });

        $response = $next($request);

        $route = $request->route()?->getName() ?? $request->path();
        $this->monitor->finish($route, $request->method(), $response->getStatusCode());

        $response->headers->set('X-Response-Time', round((microtime(true) - $this->requestStartedAt) * 1000, 2).'ms');

        return $response;
    }
}
