@props([
    'preview' => true,
    'live' => false,
    'settings' => null,
    'previewUrl' => null,
    'openUrl' => null,
    'previewLabel' => null,
    'previewSection' => null,
    'previewPages' => null,
])

@php
    $previewUrl = $previewUrl ?? route('home');
    $openUrl = $openUrl ?? $previewUrl;
@endphp

<div class="builder-shell {{ $preview ? 'builder-shell-with-preview' : '' }}" data-builder-shell data-turbo="false">
    @include('admin.builder._nav')

    <div class="builder-shell-grid">
        <div class="builder-shell-main min-w-0">
            {{ $slot }}
        </div>

        @if($preview)
            <x-admin.live-preview
                :live="$live"
                :settings="$settings"
                :preview-url="$previewUrl"
                :open-url="$openUrl"
                :preview-label="$previewLabel"
                :preview-section="$previewSection"
                :preview-pages="$previewPages ?? null"
            />
        @endif
    </div>
</div>
