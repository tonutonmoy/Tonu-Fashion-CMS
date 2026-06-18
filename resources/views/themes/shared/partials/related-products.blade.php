@if($relatedProducts->isNotEmpty())
<section class="theme-section">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">You may also like</h2>
            <a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}" class="theme-link">View more</a>
        </div>
        <div class="theme-product-grid">
            @foreach($relatedProducts as $related)
                @include('themes.shared.product-card', ['product' => $related])
            @endforeach
        </div>
    </div>
</section>
@endif
