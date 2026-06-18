@props(['showLocale' => false])

<div class="theme-pref-toggles flex items-center gap-2">
    @if($showLocale)
        <x-locale-switcher />
    @endif
    <x-color-mode-toggle />
</div>
