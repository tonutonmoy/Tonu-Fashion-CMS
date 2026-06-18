@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">{{ $category->name }}</h1>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <p class="col-span-full text-gray-500">No products in this category.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</div>
@endsection
