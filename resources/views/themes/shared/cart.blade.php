<div class="theme-container py-4 sm:py-8 max-w-3xl">
    <h1 class="theme-page-title mb-4 sm:mb-6">{{ __('common.cart') }}</h1>
    @if($items->isEmpty())
        <div class="theme-card p-6 sm:p-8 text-center">
            <p class="mb-4 text-gray-500">{{ __('common.cart_empty') ?? 'Your cart is empty.' }}</p>
            <a href="{{ route('shop.index') }}" class="theme-btn theme-btn-primary w-full sm:w-auto">{{ __('common.continue_shopping') ?? 'Continue Shopping' }}</a>
        </div>
    @else
        <div class="space-y-3 sm:space-y-4">
            @foreach($items as $item)
            <div class="theme-card p-3 sm:p-4 flex flex-col sm:flex-row gap-3 sm:gap-4">
                <div class="flex gap-3 sm:gap-4 flex-1 min-w-0">
                    @if($item->product->primary_image)
                        <img src="{{ image_url($item->product->primary_image) }}" alt="" loading="lazy" class="w-20 h-24 shrink-0 object-cover rounded-lg">
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-sm sm:text-base line-clamp-2">{{ $item->product->name }}</h3>
                        @if($item->variant)
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $item->variant->display_name }}</p>
                        @endif
                        <p class="font-semibold mt-1">{{ format_bdt($item->line_total) }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-3 border-t sm:border-t-0 border-gray-100 pt-3 sm:pt-0">
                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="theme-input w-16 text-center min-h-[44px]">
                        <button type="submit" class="theme-btn theme-btn-outline text-xs min-h-[44px] px-3">{{ __('common.update') ?? 'Update' }}</button>
                    </form>
                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 text-sm min-h-[44px] px-2">{{ __('common.remove') ?? 'Remove' }}</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        <div class="theme-card p-4 sm:p-6 mt-4 sm:mt-6">
            <div class="flex justify-between text-base sm:text-lg font-semibold mb-4">
                <span>{{ __('common.subtotal') }}</span>
                <span>{{ format_bdt($subtotal) }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="theme-btn theme-btn-primary w-full block text-center min-h-[48px]">{{ __('common.checkout') }}</a>
        </div>
    @endif
</div>
