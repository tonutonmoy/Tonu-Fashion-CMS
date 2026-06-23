@if(isset($sections['flash_sale']) && ($sections['flash_sale']['active'] ?? false))
@php
    $flash = $sections['flash_sale'];
    $flashProducts = $flash['products'] ?? collect();
@endphp
@if($flashProducts->isNotEmpty())
<section class="theme-section theme-flash-sale" id="section-flash_sale">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">⚡ Flash Sale — {{ $flash['settings']['discount'] ?? 0 }}% Off</h2>
            @if(($flash['settings']['show_countdown'] ?? false) && ! empty($flash['ends_at'] ?? $flash['settings']['end_at'] ?? null))
            <div class="theme-countdown" data-flash-countdown data-end="{{ $flash['ends_at'] ?? $flash['settings']['end_at'] ?? $flash['settings']['end_date'] }}">
                <span class="theme-countdown-label">Ends in:</span>
                <span class="theme-countdown-timer" data-flash-countdown-display>--</span>
            </div>
            @endif
        </div>
        <div class="theme-product-grid">
            @foreach($flashProducts as $product)
                @include('themes.shared.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
@endif
