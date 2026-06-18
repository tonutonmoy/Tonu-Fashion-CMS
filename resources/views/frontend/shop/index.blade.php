@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-64 flex-shrink-0">
            <form method="GET" class="card p-4 space-y-4">
                <h3 class="font-semibold">Filters</h3>
                <div>
                    <label class="label">Category</label>
                    <select name="category" class="input">
                        <option value="">All</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" @selected(($filters['category'] ?? '') === $cat->slug)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Brand</label>
                    <select name="brand" class="input">
                        <option value="">All</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Sort</label>
                    <select name="sort" class="input">
                        <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>Latest</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary w-full">Apply</button>
            </form>
        </aside>
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-6">Shop</h1>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-gray-500">No products found.</p>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
