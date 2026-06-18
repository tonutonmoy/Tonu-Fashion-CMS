<footer id="site-footer" class="theme-footer theme-footer-{{ $themeSettings->footer_style ?? 'default' }}">
    <div class="theme-container">
        <div class="theme-footer-grid">
            <div>
                @php $footerLogo = $footerSettings->logo ?? $storeSettings['logo'] ?? null; @endphp
                @if($footerLogo)
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ image_url($footerLogo) }}" alt="{{ $storeSettings['name'] ?? 'Logo' }}" loading="lazy" decoding="async" width="180" height="56" class="theme-footer-logo">
                    </a>
                @else
                    <h3 class="theme-footer-brand">{{ $storeSettings['name'] ?? 'Fashion Store' }}</h3>
                @endif
                @if($footerSettings->description ?? null)
                    <p class="theme-footer-text mt-2">{{ $footerSettings->description }}</p>
                @endif
                <p class="theme-footer-text">{{ $footerSettings->address ?? $storeSettings['address'] }}</p>
            </div>
            <div>
                <h4 class="theme-footer-heading">Contact</h4>
                @if($footerSettings->phone ?? $storeSettings['phone'] ?? null)
                    <p class="theme-footer-text">📞 {{ $footerSettings->phone ?? $storeSettings['phone'] }}</p>
                @endif
                @if($footerSettings->email ?? $storeSettings['email'] ?? null)
                    <p class="theme-footer-text">✉️ {{ $footerSettings->email ?? $storeSettings['email'] }}</p>
                @endif
            </div>
            <div>
                <h4 class="theme-footer-heading">Follow Us</h4>
                <div class="theme-footer-social">
                    @if($footerSettings->facebook_url ?? $storeSettings['facebook'] ?? null)
                        <a href="{{ $footerSettings->facebook_url ?? $storeSettings['facebook'] }}" target="_blank">Facebook</a>
                    @endif
                    @if($footerSettings->instagram_url ?? $storeSettings['instagram'] ?? null)
                        <a href="{{ $footerSettings->instagram_url ?? $storeSettings['instagram'] }}" target="_blank">Instagram</a>
                    @endif
                    @if($footerSettings->youtube_url ?? null)
                        <a href="{{ $footerSettings->youtube_url }}" target="_blank">YouTube</a>
                    @endif
                    @if($footerSettings->whatsapp_number ?? $storeSettings['whatsapp'] ?? null)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $footerSettings->whatsapp_number ?? $storeSettings['whatsapp']) }}" target="_blank">WhatsApp</a>
                    @endif
                    @if($footerSettings->messenger_link ?? $storeSettings['messenger'] ?? null)
                        <a href="{{ $footerSettings->messenger_link ?? $storeSettings['messenger'] }}" target="_blank">Messenger</a>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="theme-footer-heading">Quick Links</h4>
                <div class="theme-footer-links">
                    @include('themes.shared.partials.menu-nav', ['items' => $footerMenu ?? collect(), 'class' => 'block'])
                    <a href="{{ route('cart.index') }}">Cart</a>
                </div>
            </div>
        </div>
        <div class="theme-footer-bottom">
            {{ $footerSettings->copyright_text ?? '© '.date('Y').' '.($storeSettings['name'] ?? config('app.name')).'. All rights reserved.' }}
        </div>
    </div>
</footer>
