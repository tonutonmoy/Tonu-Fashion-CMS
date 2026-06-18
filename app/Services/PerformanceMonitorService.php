<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PerformanceMonitorService
{
    private int $queryCount = 0;

    /** @var array<int, array{sql: string, time: float}> */
    private array $queries = [];

    private float $startedAt = 0;

    public function start(): void
    {
        $this->queryCount = 0;
        $this->queries = [];
        $this->startedAt = microtime(true);
    }

    public function recordQuery(string $sql, float $timeMs): void
    {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'time' => $timeMs];
    }

    public function finish(string $route, string $method, int $status): void
    {
        if (! config('performance.profiling')) {
            return;
        }

        $durationMs = round((microtime(true) - $this->startedAt) * 1000, 2);
        $slowThreshold = (int) config('performance.slow_query_ms', 100);
        $slowQueries = array_values(array_filter(
            $this->queries,
            fn (array $q) => $q['time'] >= $slowThreshold
        ));

        $sample = [
            'route' => $route,
            'method' => $method,
            'status' => $status,
            'duration_ms' => $durationMs,
            'query_count' => $this->queryCount,
            'memory_mb' => round(memory_get_peak_usage(true) / 1048576, 2),
            'slow_queries' => array_slice($slowQueries, 0, 10),
            'recorded_at' => now()->toIso8601String(),
        ];

        $samples = Cache::get('performance.request_samples', []);
        array_unshift($samples, $sample);
        $limit = (int) config('performance.sample_limit', 100);
        $samples = array_slice($samples, 0, $limit);
        Cache::put('performance.request_samples', $samples, 86400);

        $averages = Cache::get('performance.route_averages', []);
        $key = $method.' '.$route;
        $prev = $averages[$key] ?? ['count' => 0, 'total_ms' => 0.0];
        $averages[$key] = [
            'count' => $prev['count'] + 1,
            'total_ms' => $prev['total_ms'] + $durationMs,
            'avg_ms' => round(($prev['total_ms'] + $durationMs) / ($prev['count'] + 1), 2),
            'last_ms' => $durationMs,
        ];
        Cache::put('performance.route_averages', $averages, 86400);
    }

    /** @return array<int, array<string, mixed>> */
    public function recentSamples(int $limit = 20): array
    {
        return array_slice(Cache::get('performance.request_samples', []), 0, $limit);
    }

    public function routeAverages(): array
    {
        return Cache::get('performance.route_averages', []);
    }

    public function summary(): array
    {
        $samples = $this->recentSamples(50);
        $durations = array_column($samples, 'duration_ms');
        $queries = array_column($samples, 'query_count');
        $memory = array_column($samples, 'memory_mb');

        return [
            'samples' => count($samples),
            'avg_response_ms' => $durations ? round(array_sum($durations) / count($durations), 2) : 0,
            'avg_query_count' => $queries ? round(array_sum($queries) / count($queries), 1) : 0,
            'avg_memory_mb' => $memory ? round(array_sum($memory) / count($memory), 2) : 0,
            'cache' => app(StorefrontCacheService::class)->cacheStats(),
        ];
    }
}
