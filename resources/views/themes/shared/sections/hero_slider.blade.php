@php
    $heroConfig = $sections['hero_slider']['config'] ?? [];
    $media = $heroConfig['media'] ?? [];
    $heroLayouts = config('themes.hero_content_layouts', []);
    $layout = $heroConfig['content_layout'] ?? 'centered';
    if (! array_key_exists($layout, $heroLayouts)) {
        $layout = 'centered';
    }
    $contentStyleVars = hero_slide_style_vars((object) $heroConfig);
    $autoplayMs = max(3000, (int) (($heroConfig['autoplay_seconds'] ?? 5) * 1000));
    $showTitle = (bool) ($heroConfig['show_title'] ?? true);
    $showSubtitle = (bool) ($heroConfig['show_subtitle'] ?? true);
    $showButton = (bool) ($heroConfig['show_button'] ?? true);
@endphp
@if(! empty($media))
<section class="theme-section theme-hero p-0" id="section-hero_slider">
    <div
        class="theme-hero-slider"
        data-hero-slider
        data-slides="{{ count($media) }}"
        data-autoplay="{{ $autoplayMs }}"
    >
        <div class="theme-hero-media-track">
            @foreach($media as $item)
            @include('themes.shared.sections._hero_slide', ['item' => $item, 'heroConfig' => $heroConfig, 'isFirst' => $loop->first])
            @endforeach
        </div>

        <div class="theme-hero-overlay" data-hero-live-overlay style="--hero-overlay-tint: {{ hero_overlay_rgba($heroConfig['overlay_color'] ?? null) }};"></div>

        <div
            class="theme-hero-content theme-hero-content--{{ $layout }}"
            style="{{ $contentStyleVars }}"
            data-hero-layout="{{ $layout }}"
            data-hero-live-content
        >
            <div class="theme-hero-content-inner">
                <h1 class="theme-hero-title" data-hero-live-title @if(!$showTitle || empty($heroConfig['title'])) style="display:none" @endif>{{ $heroConfig['title'] ?? '' }}</h1>
                <p class="theme-hero-subtitle" data-hero-live-subtitle @if(!$showSubtitle || empty($heroConfig['subtitle'])) style="display:none" @endif>{{ $heroConfig['subtitle'] ?? '' }}</p>
                <a
                    href="{{ $heroConfig['button_link'] ?? '#' }}"
                    class="theme-btn theme-btn-primary theme-hero-btn"
                    data-hero-live-button
                    @if(!$showButton || empty($heroConfig['button_text']) || empty($heroConfig['button_link'])) style="display:none" @endif
                >{{ $heroConfig['button_text'] ?? '' }}</a>
            </div>
        </div>

        @if(count($media) > 1)
        <button type="button" class="theme-hero-arrow theme-hero-prev" data-hero-prev aria-label="Previous slide">‹</button>
        <button type="button" class="theme-hero-arrow theme-hero-next" data-hero-next aria-label="Next slide">›</button>
        <div class="theme-hero-dots" data-hero-dots>
            @foreach($media as $item)
            <button type="button" class="theme-hero-dot {{ $loop->first ? 'is-active' : '' }}" data-hero-dot="{{ $loop->index }}" aria-label="Go to slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif
