@extends('themes.fashion-modern.layouts.app')

@section('content')
<div class="theme-container py-8 max-w-3xl">
    <h1 class="theme-page-title mb-6">Shopping Cart</h1>
    @if($items->isEmpty())
        <div class="theme-card p-8 text-center">
            <p class="mb-4 text-gray-500">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="theme-btn theme-btn-primary">Continue Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
            <div class="theme-card p-4 flex gap-4">
                @if($item->product->primary_image)
                    <img src="{{ image_url($item->product->primary_image) }}" alt="" loading="lazy" class="w-20 h-24 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                    <h3 class="font-medium">{{ $item->product->name }}</h3>
                    @if($item->variant)<p class="text-sm text-gray-500">{{ $item->variant->display_name }}</p>@endif
                    <p class="font-semibold">{{ format_bdt($item->line_total) }}</p>
                </div>
                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">@csrf @method('PATCH')
                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="theme-input w-16">
                    <button class="theme-btn theme-btn-outline text-xs">Update</button>
                </form>
                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">@csrf @method('DELETE')
                    <button class="text-red-600 text-sm">Remove</button>
                </form>
            </div>
            @endforeach
        </div>
        <div class="theme-card p-6 mt-6">
            <div class="flex justify-between text-lg font-semibold mb-4"><span>Subtotal</span><span>{{ format_bdt($subtotal) }}</span></div>
            <a href="{{ route('checkout.index') }}" class="theme-btn theme-btn-primary w-full block text-center">{{ __('common.checkout') }}</a>
        </div>
    @endif
</div>
@endsection
