@props(['class' => 'inline-flex items-center justify-end gap-1'])
<div {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>
