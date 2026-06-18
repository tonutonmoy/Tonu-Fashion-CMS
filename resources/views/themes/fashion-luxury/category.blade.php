@extends('themes.fashion-luxury.layouts.app')

@section('content')
<div class="theme-container py-8">
    <h1 class="theme-page-title mb-6">{{ $category->name }}</h1>
    <div class="theme-product-grid">
        @forelse($products as $product)
            @include('themes.shared.product-card', ['product' => $product])
        @empty
            <p class="col-span-full text-gray-500">No products found.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</div>
@endsection

