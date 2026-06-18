<footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div>
            <h3 class="text-white font-semibold mb-3">{{ $storeSettings['name'] ?? 'Fashion Store' }}</h3>
            <p class="text-sm">{{ $storeSettings['address'] ?? 'Dhaka, Bangladesh' }}</p>
        </div>
        <div>
            <h4 class="text-white font-medium mb-3">Contact</h4>
            @if($storeSettings['phone'] ?? null)<p class="text-sm">📞 {{ $storeSettings['phone'] }}</p>@endif
            @if($storeSettings['email'] ?? null)<p class="text-sm">✉️ {{ $storeSettings['email'] }}</p>@endif
        </div>
        <div>
            <h4 class="text-white font-medium mb-3">Follow Us</h4>
            <div class="flex gap-3 text-sm">
                @if($storeSettings['facebook'] ?? null)<a href="{{ $storeSettings['facebook'] }}" target="_blank" class="hover:text-white">Facebook</a>@endif
                @if($storeSettings['instagram'] ?? null)<a href="{{ $storeSettings['instagram'] }}" target="_blank" class="hover:text-white">Instagram</a>@endif
                @if($storeSettings['whatsapp'] ?? null)<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeSettings['whatsapp']) }}" target="_blank" class="hover:text-white">WhatsApp</a>@endif
            </div>
        </div>
        <div>
            <h4 class="text-white font-medium mb-3">Quick Links</h4>
            <div class="flex flex-col gap-2 text-sm">
                <a href="{{ route('shop.index') }}" class="hover:text-white">Shop</a>
                <a href="{{ route('cart.index') }}" class="hover:text-white">Cart</a>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} {{ $storeSettings['name'] ?? config('app.name') }}. All rights reserved.
    </div>
</footer>
