@php
    $embedUrl = ($item['type'] ?? '') === 'video' ? hero_video_embed_url($item['video_url'] ?? null) : null;
    $isVideoFile = hero_video_is_file($item['video_url'] ?? null);
    $ytThumb = hero_youtube_thumbnail($item['video_url'] ?? null);
@endphp
<div class="theme-hero-slide {{ ($isFirst ?? false) ? 'is-active' : '' }}" data-hero-slide>
    @if($embedUrl && ! $isVideoFile && $ytThumb)
        <img src="{{ $ytThumb }}" alt="{{ $heroConfig['title'] ?? 'Hero' }}" loading="{{ ($isFirst ?? false) ? 'eager' : 'lazy' }}" @if($isFirst ?? false) fetchpriority="high" @endif decoding="async" class="theme-hero-bg theme-hero-video-poster">
    @elseif($embedUrl)
        <div class="theme-hero-bg theme-hero-video">
            @if($isVideoFile)
                <video class="theme-hero-video-file" src="{{ $embedUrl }}" autoplay muted loop playsinline></video>
            @else
                <iframe src="{{ $embedUrl }}" title="{{ $heroConfig['title'] ?? 'Hero' }}" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen tabindex="-1"></iframe>
            @endif
        </div>
    @elseif(! empty($item['desktop_image']))
        <picture>
            @if(! empty($item['mobile_image']))
                <source media="(max-width: 768px)" srcset="{{ image_url($item['mobile_image']) }}">
            @endif
            <img src="{{ image_url($item['desktop_image']) }}" alt="{{ $heroConfig['title'] ?? 'Hero' }}" loading="{{ ($isFirst ?? false) ? 'eager' : 'lazy' }}" @if($isFirst ?? false) fetchpriority="high" @endif decoding="async" class="theme-hero-bg">
        </picture>
    @else
        <div class="theme-hero-bg theme-hero-placeholder"></div>
    @endif
</div>
