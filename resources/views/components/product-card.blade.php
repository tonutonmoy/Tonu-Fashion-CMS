@props(['product'])

<a href="{{ route('products.show', $product->slug) }}" class="group card overflow-hidden hover:shadow-md transition-shadow">
    <div class="aspect-[3/4] bg-gray-100 overflow-hidden relative">
        @if($product->primary_image)
            <img src="{{ image_url($product->primary_image) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">No Image</div>
        @endif
        @if($product->isOnSale())
            <span class="absolute top-2 left-2 badge bg-brand-600 text-white">Sale</span>
        @endif
    </div>
    <div class="p-3 sm:p-4">
        <p class="text-xs text-gray-500 mb-1">{{ $product->category?->name }}</p>
        <h3 class="font-medium text-sm sm:text-base line-clamp-2 group-hover:text-brand-600">{{ $product->name }}</h3>
        <div class="mt-2 flex items-center gap-2">
            <span class="font-semibold text-gray-900">{{ format_bdt($product->effective_price) }}</span>
            @if($product->isOnSale())
                <span class="text-sm text-gray-400 line-through">{{ format_bdt($product->regular_price) }}</span>
            @endif
        </div>
    </div>
</a>
