@props(['theme' => $activeTheme ?? 'fashion-modern'])

@if($storeSettings['favicon'] ?? null)
    <link rel="icon" href="{{ image_url($storeSettings['favicon']) }}">
@endif
<meta name="turbo-prefetch-cache-time" content="300">
@if(! config('performance.use_system_fonts'))
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="{{ theme()->googleFontUrl() }}" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="{{ theme()->googleFontUrl() }}" rel="stylesheet"></noscript>
@endif
<link rel="dns-prefetch" href="https://i.ibb.co">
<style>
    .turbo-progress{position:fixed;top:0;left:0;height:3px;width:0;background:var(--theme-primary,#111827);z-index:99999;opacity:0;transition:width .15s ease,opacity .2s ease;pointer-events:none}
    html.is-turbo-navigating .theme-main{opacity:.92;transition:opacity .12s ease}
    html.is-turbo-navigating .turbo-progress{opacity:1}
    {!! theme()->cssVariables() !!}
</style>
@vite(['resources/css/app.css', 'resources/js/storefront.js'])
<link rel="stylesheet" href="{{ theme_asset('theme.css') }}">
