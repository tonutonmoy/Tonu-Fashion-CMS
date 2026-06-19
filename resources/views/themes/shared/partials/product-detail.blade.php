@php
    $images = $product->images->isNotEmpty()
        ? $product->images->sortByDesc('is_primary')->values()
        : collect();
    $activeVariants = $product->variants->where('status', 'active')->values();
    $variantPayload = $activeVariants->map(fn ($v) => [
        'id' => $v->id,
        'size' => $v->size,
        'color' => $v->color,
        'price_label' => format_bdt($v->price),
        'image' => $v->image ? image_url($v->image) : null,
        'stock' => $v->stock,
    ]);
    $sizeOptions = $activeVariants->pluck('size')->filter()->unique()->values();
    $colorOptions = $activeVariants->pluck('color')->filter()->unique()->values();
    $showVariantPicker = $activeVariants->count() > 1
        && ($sizeOptions->isNotEmpty() || $colorOptions->isNotEmpty());
    $autoVariant = $activeVariants->count() === 1 ? $activeVariants->first() : null;
@endphp

<div class="theme-container py-4 sm:py-8 pb-24 lg:pb-8">
    <nav class="theme-breadcrumb text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6 flex flex-wrap items-center gap-1 sm:gap-2">
        <a href="{{ route('home') }}" class="hover:text-gray-900">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-gray-900">Shop</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-gray-900">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <p class="text-xs text-gray-400 mb-4 hidden sm:block">/{{ $product->slug }}</p>

    <div class="theme-product-detail">
        <div class="theme-product-gallery-wrap" data-product-gallery>
            <div class="theme-product-gallery-main relative overflow-hidden rounded-2xl bg-gray-100">
                @if($images->isNotEmpty())
                    <img
                        src="{{ image_url($images->first()->path) }}"
                        alt="{{ $product->name }}"
                        class="theme-detail-image w-full aspect-[4/5] object-cover"
                        data-gallery-main
                        id="product-gallery-main"
                        width="800"
                        height="1000"
                        fetchpriority="high"
                        decoding="async"
                    >
                    <div class="theme-gallery-zoom hidden lg:block absolute top-0 left-[calc(100%+1rem)] w-[min(28rem,40vw)] h-full border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-xl z-10" data-gallery-zoom>
                        <div class="w-full h-full bg-no-repeat" data-gallery-zoom-inner></div>
                    </div>
                @else
                    <div class="theme-detail-placeholder aspect-[4/5] flex items-center justify-center">No Image</div>
                @endif
            </div>

            @if($images->count() > 1)
                <div class="theme-product-thumbs flex gap-2 mt-3 overflow-x-auto">
                    @foreach($images as $image)
                        <button
                            type="button"
                            class="theme-gallery-thumb shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 {{ $loop->first ? 'is-active border-gray-900' : 'border-transparent' }}"
                            data-gallery-thumb="{{ image_url($image->path) }}"
                            data-gallery-alt="{{ $product->name }}"
                        >
                            <img src="{{ image_url($image->path) }}" alt="" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="theme-product-info">
            @if($product->category)
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="theme-product-category hover:underline">{{ $product->category->name }}</a>
            @endif
            <h1 class="theme-page-title">{{ $product->name }}</h1>

            <div class="theme-product-price">
                <span class="theme-price-current text-2xl" data-variant-price>{{ format_bdt($product->effective_price) }}</span>
                @if($product->isOnSale())
                    <span class="theme-price-old">{{ format_bdt($product->regular_price) }}</span>
                @endif
            </div>

            <p class="theme-product-desc">{{ $product->short_description }}</p>

            <form action="{{ route('cart.store') }}" method="POST" class="theme-form space-y-4" data-add-to-cart id="product-add-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($activeVariants->isNotEmpty())
                    @if($showVariantPicker)
                    <div class="space-y-4" data-product-variants data-variants='@json($variantPayload)'>
                        @if($sizeOptions->isNotEmpty())
                            <div>
                                <p class="text-sm font-medium mb-2">Size</p>
                                <div class="flex flex-wrap gap-2" data-size-group></div>
                            </div>
                        @endif
                        @if($colorOptions->isNotEmpty())
                            <div>
                                <p class="text-sm font-medium mb-2">Color</p>
                                <div class="flex flex-wrap gap-2" data-color-group></div>
                            </div>
                        @endif
                        <input type="hidden" name="product_variant_id" data-variant-id>
                    </div>
                    @elseif($autoVariant)
                        <input type="hidden" name="product_variant_id" value="{{ $autoVariant->id }}">
                    @else
                        <input type="hidden" name="product_variant_id" value="{{ $activeVariants->first()->id }}">
                    @endif
                @endif

                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium">Qty</label>
                    <input type="number" name="quantity" value="1" min="1" max="99" class="theme-input w-24">
                </div>

                <div class="theme-product-actions grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="submit" class="theme-btn theme-btn-primary w-full min-h-[48px]" @disabled(!$product->inStock())>
                        {{ $product->inStock() ? __('common.add_to_cart') : __('common.out_of_stock') }}
                    </button>
                    <button
                        type="button"
                        class="theme-btn theme-btn-outline w-full min-h-[48px]"
                        data-buy-now
                        data-checkout-url="{{ route('checkout.index') }}"
                        @disabled(!$product->inStock())
                    >
                        {{ __('common.checkout') }}
                    </button>
                </div>
            </form>

            @auth
                <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="mt-3">@csrf
                    <button class="theme-btn theme-btn-outline w-full">{{ $inWishlist ?? false ? '♥ In Wishlist' : '♡ Add to Wishlist' }}</button>
                </form>
            @endauth

            <div class="theme-product-full-desc mt-8">{!! nl2br(e($product->description)) !!}</div>
        </div>
    </div>
</div>

<div class="mobile-atc-bar lg:hidden" data-mobile-atc>
    <div class="flex items-center gap-2 sm:gap-3">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold truncate">{{ $product->name }}</p>
            <p class="text-sm text-gray-600" data-variant-price>{{ format_bdt($product->effective_price) }}</p>
        </div>
        <button type="button" class="btn-secondary shrink-0 min-h-[44px] px-3 sm:px-4 text-sm" data-mobile-atc-add @disabled(!$product->inStock())>
            {{ __('common.add_to_cart') }}
        </button>
        <button
            type="button"
            class="btn-primary shrink-0 min-h-[44px] px-3 sm:px-4 text-sm"
            data-buy-now
            data-checkout-url="{{ route('checkout.index') }}"
            @disabled(!$product->inStock())
        >
            {{ __('common.checkout') }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    window.productVariants = @json($variantPayload);
</script>
@endpush
