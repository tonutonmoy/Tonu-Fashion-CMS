@props(['class' => ''])
@php
    $current = $currentLocale ?? app()->getLocale();
    $locales = $supportedLocales ?? config('locales.supported', ['en', 'bn']);
@endphp
<div class="locale-switcher theme-locale-switcher inline-flex items-center p-1 rounded-full border border-gray-200/90 bg-white/70 backdrop-blur-md shadow-sm {{ $class }}" role="group" aria-label="{{ __('common.language') }}">
    @foreach($locales as $locale)
    <a
        href="{{ route('preferences.locale', $locale) }}"
        class="theme-locale-btn {{ $current === $locale ? 'is-active' : '' }}"
        @if($current === $locale) aria-current="true" @endif
    >{{ strtoupper($locale) }}</a>
    @endforeach
</div>
