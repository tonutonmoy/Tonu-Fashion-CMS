@props(['class' => ''])
@php
    $mode = $colorMode ?? 'light';
    $target = $mode === 'dark' ? 'light' : 'dark';
    $icon = $mode === 'dark' ? 'sun' : 'moon';
    $label = $mode === 'dark' ? __('common.light_mode') : __('common.dark_mode');
@endphp
<a
    href="{{ route('preferences.color-mode', $target) }}"
    class="color-mode-toggle theme-color-mode-single theme-icon-btn {{ $class }}"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    data-color-mode-toggle
>
    <x-admin.icon :name="$icon" class="w-5 h-5" />
</a>
