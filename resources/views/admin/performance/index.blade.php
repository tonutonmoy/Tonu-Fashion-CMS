@extends('layouts.admin')

@section('title', 'Performance Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Performance Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Query count, response time, cache hit rate, memory usage</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.performance.warm-cache') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Warm Cache</button>
            </form>
            <form method="POST" action="{{ route('admin.performance.indexes') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Create Mongo Indexes</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="admin-stat-card">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg Response</p>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['avg_response_ms'] }}ms</p>
        </div>
        <div class="admin-stat-card">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg DB Queries</p>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['avg_query_count'] }}</p>
        </div>
        <div class="admin-stat-card">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Cache Hit Rate</p>
            <p class="text-2xl font-bold text-gray-900">{{ $cacheStats['hit_rate'] }}%</p>
        </div>
        <div class="admin-stat-card">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg Memory</p>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['avg_memory_mb'] }} MB</p>
        </div>
        <div class="admin-stat-card">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Cache TTL</p>
            <p class="text-2xl font-bold text-gray-900">{{ $cacheTtl }}s</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="admin-card">
            <h2 class="admin-card-title">Slowest Routes (avg)</h2>
            <div class="overflow-x-auto">
                <table class="admin-table text-sm">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Avg</th>
                            <th>Last</th>
                            <th>Hits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routeAverages as $route => $data)
                        <tr>
                            <td class="font-mono text-xs">{{ $route }}</td>
                            <td>{{ $data['avg_ms'] }}ms</td>
                            <td>{{ $data['last_ms'] }}ms</td>
                            <td>{{ $data['count'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-gray-500">No samples yet. Visit storefront pages first.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <h2 class="admin-card-title">Recent Requests</h2>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="admin-table text-sm">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Time</th>
                            <th>Queries</th>
                            <th>Memory</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($samples as $sample)
                        <tr>
                            <td class="font-mono text-xs">{{ $sample['route'] }}</td>
                            <td @class(['text-red-600 font-semibold' => $sample['duration_ms'] > 800])>{{ $sample['duration_ms'] }}ms</td>
                            <td>{{ $sample['query_count'] }}</td>
                            <td>{{ $sample['memory_mb'] }}MB</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-gray-500">No request samples recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @php $slow = collect($samples)->flatMap(fn ($s) => $s['slow_queries'] ?? [])->take(10); @endphp
    @if($slow->isNotEmpty())
    <div class="admin-card">
        <h2 class="admin-card-title">Slow Queries (&gt; {{ config('performance.slow_query_ms') }}ms)</h2>
        <ul class="space-y-2 text-xs font-mono text-gray-700">
            @foreach($slow as $query)
            <li class="p-2 bg-gray-50 rounded">{{ $query['time'] }}ms — {{ \Illuminate\Support\Str::limit($query['sql'], 200) }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection
