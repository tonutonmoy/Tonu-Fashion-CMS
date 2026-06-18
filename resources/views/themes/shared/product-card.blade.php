@props(['product'])
<a href="{{ route('products.show', $product->slug) }}" class="theme-product-card group">
    <div class="theme-product-image">
        @if($product->primary_image)
            <img src="{{ image_url($product->primary_image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="400" height="533" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-product.svg') }}';">
        @else
            <img src="{{ asset('images/placeholder-product.svg') }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="400" height="533" class="w-full h-full object-cover">
        @endif
        @if($product->isOnSale())
            <span class="theme-badge-sale">Sale</span>
        @endif
    </div>
    <div class="theme-product-body">
        <p class="theme-product-category">{{ $product->category?->name }}</p>
        <h3 class="theme-product-title">{{ trans_field($product, 'name') }}</h3>
        <div class="theme-product-price">
            <span class="theme-price-current">{{ format_bdt($product->effective_price) }}</span>
            @if($product->isOnSale())
                <span class="theme-price-old">{{ format_bdt($product->regular_price) }}</span>
            @endif
        </div>
    </div>
</a>
