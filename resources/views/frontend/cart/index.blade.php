@extends('layouts.frontend')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>
    @if($items->isEmpty())
        <div class="card p-8 text-center">
            <p class="text-gray-500 mb-4">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary">Continue Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
            <div class="card p-4 flex flex-col sm:flex-row gap-4">
                @if($item->product->primary_image)
                    <img src="{{ image_url($item->product->primary_image) }}" alt="" loading="lazy" class="w-20 h-24 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <h3 class="font-medium">{{ $item->product->name }}</h3>
                    @if($item->variant)<p class="text-sm text-gray-500">{{ $item->variant->display_name }}</p>@endif
                    <p class="font-semibold mt-1">{{ format_bdt($item->line_total) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="input w-16 text-center">
                        <button type="submit" class="btn-secondary text-xs">Update</button>
                    </form>
                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 text-sm hover:underline">Remove</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card p-6 mt-6">
            <div class="flex justify-between text-lg font-semibold mb-4">
                <span>Subtotal</span>
                <span>{{ format_bdt($subtotal) }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-primary w-full block text-center">{{ __('common.checkout') }}</a>
        </div>
    @endif
</div>
@endsection
