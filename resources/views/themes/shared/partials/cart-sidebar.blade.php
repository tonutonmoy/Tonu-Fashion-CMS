<div id="cart-sidebar-overlay" class="fixed inset-0 bg-black/40 z-[60] hidden" data-turbo-permanent aria-hidden="true"></div>
<aside id="cart-sidebar" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-[70] transform translate-x-full transition-transform duration-300 flex flex-col" data-turbo-permanent aria-label="Shopping cart">
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">Your Cart</h2>
        <button type="button" id="cart-sidebar-close" class="p-2 hover:bg-gray-100 rounded-lg" aria-label="Close cart">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div id="cart-sidebar-items" class="flex-1 overflow-y-auto p-4">
        <p class="text-sm text-gray-500 py-8 text-center">Loading cart...</p>
    </div>
    <div class="border-t border-gray-200 p-4 space-y-3 bg-gray-50">
        <div class="flex justify-between text-sm font-medium">
            <span>Subtotal</span>
            <span id="cart-sidebar-subtotal">৳0</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="btn-primary w-full text-center block">Checkout</a>
        <a href="{{ route('cart.index') }}" class="btn-secondary w-full text-center block">View full cart</a>
    </div>
</aside>
