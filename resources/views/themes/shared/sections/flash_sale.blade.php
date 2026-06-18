@if(isset($sections['flash_sale']) && ($sections['flash_sale']['active'] ?? false))
@php $flash = $sections['flash_sale']; @endphp
<section class="theme-section theme-flash-sale">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">⚡ Flash Sale — {{ $flash['settings']['discount'] ?? 0 }}% Off</h2>
            @if($flash['settings']['show_countdown'] ?? false)
            <div class="theme-countdown" data-end="{{ $flash['settings']['end_date'] }}">
                <span class="theme-countdown-label">Ends in:</span>
                <span id="flash-countdown" class="theme-countdown-timer"></span>
            </div>
            @endif
        </div>
        <div class="theme-product-grid">
            @foreach($flash['products'] as $product)
                @include('themes.shared.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
