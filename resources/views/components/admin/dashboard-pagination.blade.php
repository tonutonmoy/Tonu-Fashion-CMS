@props([
    'page' => 1,
    'total' => 0,
    'perPage' => 10,
    'param' => 'page',
])

@php
    $perPage = max(1, (int) $perPage);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min((int) $page, $pages));
    $from = $total === 0 ? 0 : (($page - 1) * $perPage + 1);
    $to = min($page * $perPage, $total);
@endphp

<div {{ $attributes->merge(['class' => 'px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm']) }}>
    <span class="text-gray-500">Showing {{ $from }}–{{ $to }} of {{ $total }}</span>
    <div class="flex items-center gap-3">
        @if($page > 1)
        <a href="{{ request()->fullUrlWithQuery([$param => $page - 1]) }}" class="text-brand-600 hover:underline">← Prev</a>
        @else
        <span class="text-gray-300 select-none">← Prev</span>
        @endif
        <span class="text-gray-500">Page {{ $page }} / {{ $pages }}</span>
        @if($page < $pages)
        <a href="{{ request()->fullUrlWithQuery([$param => $page + 1]) }}" class="text-brand-600 hover:underline">Next →</a>
        @else
        <span class="text-gray-300 select-none">Next →</span>
        @endif
    </div>
</div>
