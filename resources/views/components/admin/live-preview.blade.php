@props([
    'live' => false,
    'settings' => null,
    'previewUrl' => null,
    'openUrl' => null,
    'previewLabel' => null,
    'previewSection' => null,
])

@php
    $s = $settings ?? ($live ? app(\App\Services\ThemeCustomizerService::class)->get() : null);
    $previewUrl = $previewUrl ?? builder_preview_url();
    $openUrl = $openUrl ?? $previewUrl;
    $previewHash = $previewSection ? '#'.ltrim($previewSection, '#') : '';
    if ($previewHash && ! str_contains($previewUrl, $previewHash)) {
        $previewUrl .= $previewHash;
    }
@endphp

<aside class="builder-preview hidden xl:block" data-builder-preview @if($previewSection) data-preview-section="{{ $previewSection }}" @endif>
    <div class="builder-preview-panel card p-3">
        <div class="flex items-center justify-between gap-2 mb-2">
            <div class="min-w-0">
                <h3 class="font-semibold text-sm">Live Preview</h3>
                <p class="text-xs text-gray-500 truncate" data-preview-label title="{{ $previewLabel ?? $previewUrl }}">
                    {{ $previewLabel ?? 'Your storefront' }}
                </p>
            </div>
            <button type="button" class="text-xs text-brand-600 hover:underline shrink-0" data-preview-refresh>Refresh</button>
            @if($previewSection)
            <button type="button" class="text-xs text-gray-600 hover:underline shrink-0" data-preview-goto-section="{{ $previewSection }}">Show Hero</button>
            @endif
        </div>

        @if($live && $s)
            <div class="flex h-1.5 rounded-full overflow-hidden mb-2" data-theme-preview>
                <span class="flex-1" data-swatch-primary style="background: {{ $s->primary_color }}"></span>
                <span class="flex-1" data-swatch-secondary style="background: {{ $s->secondary_color }}"></span>
                <span class="flex-1" data-swatch-accent style="background: {{ $s->accent_color ?? '#f59e0b' }}"></span>
            </div>
        @endif

        <div class="builder-preview-devices flex gap-1 mb-2">
            <button type="button" class="builder-preview-device is-active" data-preview-device="desktop">Desktop</button>
            <button type="button" class="builder-preview-device" data-preview-device="mobile">Mobile</button>
        </div>

        <div class="builder-preview-frame is-desktop" data-preview-frame>
            <div class="builder-preview-scale-wrap" data-preview-scale-wrap>
                <iframe src="{{ $previewUrl }}" data-theme-preview-iframe data-preview-src="{{ strtok($previewUrl, '#') }}" @if($previewSection) data-preview-hash="#{{ ltrim($previewSection, '#') }}" @endif title="Live preview" loading="eager"></iframe>
            </div>
        </div>

        <a href="{{ $openUrl }}" target="_blank" data-preview-open class="btn-secondary w-full block text-center text-sm mt-2">Open in New Tab ↗</a>
    </div>
</aside>

<div class="xl:hidden mt-4 card p-3" data-builder-preview-mobile>
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-sm">Live Preview</h3>
        <button type="button" class="text-xs text-brand-600" data-preview-refresh>Refresh</button>
    </div>
    <div class="builder-preview-frame is-mobile" data-preview-frame>
        <iframe src="{{ $previewUrl }}" data-theme-preview-iframe data-preview-src="{{ strtok($previewUrl, '#') }}" @if($previewSection) data-preview-hash="#{{ ltrim($previewSection, '#') }}" @endif title="Live preview" loading="eager"></iframe>
    </div>
</div>
