@php
    $key = $sectionKey ?? 'featured_products';
    $products = $sections[$key]['products'] ?? collect();
    $title = match($key) {
        'new_arrivals' => 'New Arrivals',
        'best_sellers' => 'Best Sellers',
        default => 'Featured Products',
    };
@endphp
@if($products->isNotEmpty())
<section class="theme-section">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">{{ $title }}</h2>
            <a href="{{ route('shop.index') }}" class="theme-link">View All</a>
        </div>
        <div class="theme-product-grid">
            @foreach($products as $product)
                @include('themes.shared.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
