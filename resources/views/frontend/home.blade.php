@extends('layouts.frontend')

@section('content')
<section class="bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center">
        <h1 class="text-3xl sm:text-5xl font-bold mb-4">{{ $storeSettings['name'] ?? 'Premium Fashion' }}</h1>
        <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">Discover the latest trends in Bangladesh fashion. Quality fabrics, stylish designs, cash on delivery nationwide.</p>
        <a href="{{ route('shop.index') }}" class="btn-primary text-lg px-8 py-3">Shop Now</a>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <h2 class="text-2xl font-bold mb-6">Shop by Category</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
        <a href="{{ route('categories.show', $category) }}" class="card p-4 text-center hover:shadow-md transition-shadow">
            @if($category->image)
                <img src="{{ image_url($category->image) }}" alt="{{ $category->name }}" loading="lazy" class="w-16 h-16 mx-auto rounded-full object-cover mb-2">
            @endif
            <span class="text-sm font-medium">{{ $category->name }}</span>
        </a>
        @endforeach
    </div>
</section>
@endif

<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Featured Products</h2>
        <a href="{{ route('shop.index', ['featured' => 1]) }}" class="text-sm text-brand-600 hover:underline">View All</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($featuredProducts as $product)
            <x-product-card :product="$product" />
        @empty
            <p class="col-span-full text-gray-500">No featured products yet.</p>
        @endforelse
    </div>
</section>
@endsection
