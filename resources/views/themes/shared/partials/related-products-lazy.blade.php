<div
    data-lazy-section
    data-lazy-url="{{ route('products.related', $productSlug) }}"
    class="theme-section"
    aria-busy="true"
>
    <div class="theme-container py-6">
        <div class="h-8 w-48 bg-gray-200/80 rounded animate-pulse mb-6"></div>
        <div class="theme-product-grid">
            @for ($i = 0; $i < 4; $i++)
                <div class="space-y-3">
                    <div class="aspect-[3/4] bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 w-3/4 bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-4 w-1/3 bg-gray-100 rounded animate-pulse"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
