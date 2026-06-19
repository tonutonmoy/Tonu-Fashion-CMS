@extends(theme_layout())

@section('content')
<div class="theme-container py-4 sm:py-6 lg:py-8">
    <div class="theme-shop-layout">
        <div id="shop-filter-overlay" class="shop-filter-overlay md:hidden hidden" aria-hidden="true"></div>

        <aside id="shop-filter-panel" class="theme-shop-filters card p-4">
            <div class="flex items-center justify-between mb-4 md:hidden">
                <h3 class="font-semibold">Filters</h3>
                <button type="button" id="shop-filter-close" class="theme-icon-btn" aria-label="Close filters">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="shop-filter-form" method="GET" action="{{ route('shop.index') }}" class="space-y-5">
                @if(!empty($filters['q']))
                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                @endif

                <div class="hidden md:block">
                    <h3 class="font-semibold mb-3">Filters</h3>
                    <p class="text-xs text-gray-500">Updates automatically</p>
                </div>

                <div>
                    <label class="label">Category</label>
                    <select name="category" class="input shop-filter-input">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" @selected(($filters['category'] ?? '') === $cat->slug)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Brand</label>
                    <select name="brand" class="input shop-filter-input">
                        <option value="">All brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label flex items-center gap-2">
                        <input type="checkbox" name="featured" value="1" class="shop-filter-input rounded" @checked(!empty($filters['featured']))>
                        Featured only
                    </label>
                </div>

                <div>
                    <label class="label">Price range ({{ config('fashion.currency_symbol', '৳') }})</label>
                    @php
                        $minBound = $priceBounds['min'];
                        $maxBound = $priceBounds['max'];
                        $curMin = (int) ($filters['min_price'] ?? $minBound);
                        $curMax = (int) ($filters['max_price'] ?? $maxBound);
                    @endphp
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span id="price-min-label">{{ format_bdt($curMin) }}</span>
                        <span id="price-max-label">{{ format_bdt($curMax) }}</span>
                    </div>
                    <div class="space-y-3">
                        <input type="range" id="price-min-slider" class="w-full shop-filter-input" min="{{ $minBound }}" max="{{ $maxBound }}" value="{{ $curMin }}" step="50">
                        <input type="range" id="price-max-slider" class="w-full shop-filter-input" min="{{ $minBound }}" max="{{ $maxBound }}" value="{{ $curMax }}" step="50">
                    </div>
                    <input type="hidden" name="min_price" id="min_price" value="{{ $curMin }}">
                    <input type="hidden" name="max_price" id="max_price" value="{{ $curMax }}">
                </div>

                <div>
                    <label class="label">Sort by</label>
                    <select name="sort" class="input shop-filter-input">
                        <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
                        <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name A–Z</option>
                    </select>
                </div>

                <a href="{{ route('shop.index') }}" class="btn-secondary w-full text-center block">Clear filters</a>
            </form>
        </aside>

        <div class="theme-shop-results flex-1 min-w-0">
            <button type="button" id="shop-filter-toggle" class="shop-filter-toggle md:hidden mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M10 12h4"/></svg>
                Filters & Sort
            </button>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
                <h1 class="theme-page-title mb-0">
                    @if(!empty($filters['q']))
                        Search: “{{ $filters['q'] }}”
                    @else
                        Shop
                    @endif
                </h1>
            </div>
            <div id="shop-results">
                @include('themes.shared.partials.shop-products', ['products' => $products])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.shopPriceBounds = @json($priceBounds);
</script>
@endpush
