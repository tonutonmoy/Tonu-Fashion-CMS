<div id="shop-results-count" class="text-sm text-gray-500 mb-4">
    {{ $products->total() }} product{{ $products->total() === 1 ? '' : 's' }} found
</div>
<div id="shop-product-grid" class="theme-product-grid">
    @forelse($products as $product)
        @include('themes.shared.product-card', ['product' => $product])
    @empty
        <p class="col-span-full text-gray-500 py-12 text-center">No products match your filters.</p>
    @endforelse
</div>
<div id="shop-pagination" class="mt-8">
    {{ $products->links() }}
</div>
