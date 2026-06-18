@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            @foreach($product->images as $image)
                <img src="{{ image_url($image->path) }}" alt="{{ $product->name }}" loading="lazy" class="w-full rounded-xl {{ $loop->first ? '' : 'hidden sm:block' }}">
            @endforeach
            @if($product->images->isEmpty())
                <div class="aspect-[3/4] bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">No Image</div>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ $product->category?->name }} @if($product->brand) · {{ $product->brand->name }} @endif</p>
            <h1 class="text-2xl sm:text-3xl font-bold mt-1">{{ $product->name }}</h1>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-2xl font-bold">{{ format_bdt($product->effective_price) }}</span>
                @if($product->isOnSale())
                    <span class="text-lg text-gray-400 line-through">{{ format_bdt($product->regular_price) }}</span>
                @endif
            </div>
            @if($product->review_count > 0)
                <p class="text-sm text-yellow-600 mt-2">★ {{ number_format($product->avg_rating, 1) }} ({{ $product->review_count }} reviews)</p>
            @endif
            <p class="text-gray-600 mt-4">{{ $product->short_description }}</p>

            <form action="{{ route('cart.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if($product->variants->isNotEmpty())
                    <div>
                        <label class="label">Size / Color</label>
                        <select name="product_variant_id" class="input" required>
                            @foreach($product->variants->where('status', 'active') as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->display_name }} ({{ format_bdt($variant->price) }}) - Stock: {{ $variant->stock }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="label">Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" max="99" class="input w-24">
                </div>
                <button type="submit" class="btn-primary w-full sm:w-auto" @disabled(!$product->inStock())>
                    {{ $product->inStock() ? 'Add to Cart' : 'Out of Stock' }}
                </button>
            </form>
            @auth
                <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-secondary">{{ $inWishlist ? '♥ In Wishlist' : '♡ Wishlist' }}</button>
                </form>
            @endauth

            <div class="prose prose-sm mt-8">{!! nl2br(e($product->description)) !!}</div>
        </div>
    </div>

    @if($product->approvedReviews->isNotEmpty())
    <section class="mt-12">
        <h2 class="text-xl font-bold mb-4">Customer Reviews</h2>
        <div class="space-y-4">
            @foreach($product->approvedReviews as $review)
            <div class="card p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-yellow-500">{{ str_repeat('★', $review->rating) }}</span>
                    <span class="text-sm text-gray-500">{{ $review->user->name }}</span>
                </div>
                <p class="text-gray-700">{{ $review->comment }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    @auth
    <section class="mt-8 card p-6">
        <h3 class="font-semibold mb-4">Write a Review</h3>
        <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="label">Rating</label>
                <select name="rating" class="input w-32" required>
                    @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>@endfor
                </select>
            </div>
            <div>
                <label class="label">Comment</label>
                <textarea name="comment" rows="3" class="input"></textarea>
            </div>
            <button type="submit" class="btn-primary">Submit Review</button>
        </form>
    </section>
    @endauth
</div>
@endsection
