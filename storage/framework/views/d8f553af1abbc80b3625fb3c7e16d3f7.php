<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'live' => false,
    'settings' => null,
    'previewUrl' => null,
    'openUrl' => null,
    'previewLabel' => null,
    'previewSection' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'live' => false,
    'settings' => null,
    'previewUrl' => null,
    'openUrl' => null,
    'previewLabel' => null,
    'previewSection' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $s = $settings ?? ($live ? app(\App\Services\ThemeCustomizerService::class)->get() : null);
    $previewUrl = $previewUrl ?? builder_preview_url();
    $openUrl = $openUrl ?? $previewUrl;
    $previewHash = $previewSection ? '#'.ltrim($previewSection, '#') : '';
    if ($previewHash && ! str_contains($previewUrl, $previewHash)) {
        $previewUrl .= $previewHash;
    }
?>

<aside class="builder-preview hidden xl:block" data-builder-preview <?php if($previewSection): ?> data-preview-section="<?php echo e($previewSection); ?>" <?php endif; ?>>
    <div class="builder-preview-panel card p-3">
        <div class="flex items-center justify-between gap-2 mb-2">
            <div class="min-w-0">
                <h3 class="font-semibold text-sm">Live Preview</h3>
                <p class="text-xs text-gray-500 truncate" data-preview-label title="<?php echo e($previewLabel ?? $previewUrl); ?>">
                    <?php echo e($previewLabel ?? 'Your storefront'); ?>

                </p>
            </div>
            <button type="button" class="text-xs text-brand-600 hover:underline shrink-0" data-preview-refresh>Refresh</button>
            <?php if($previewSection): ?>
            <button type="button" class="text-xs text-gray-600 hover:underline shrink-0" data-preview-goto-section="<?php echo e($previewSection); ?>">Show Hero</button>
            <?php endif; ?>
        </div>

        <?php if($live && $s): ?>
            <div class="flex h-1.5 rounded-full overflow-hidden mb-2" data-theme-preview>
                <span class="flex-1" data-swatch-primary style="background: <?php echo e($s->primary_color); ?>"></span>
                <span class="flex-1" data-swatch-secondary style="background: <?php echo e($s->secondary_color); ?>"></span>
                <span class="flex-1" data-swatch-accent style="background: <?php echo e($s->accent_color ?? '#f59e0b'); ?>"></span>
            </div>
        <?php endif; ?>

        <div class="builder-preview-devices flex gap-1 mb-2">
            <button type="button" class="builder-preview-device is-active" data-preview-device="desktop">Desktop</button>
            <button type="button" class="builder-preview-device" data-preview-device="mobile">Mobile</button>
        </div>

        <div class="builder-preview-frame is-desktop" data-preview-frame>
            <div class="builder-preview-scale-wrap" data-preview-scale-wrap>
                <iframe src="<?php echo e($previewUrl); ?>" data-theme-preview-iframe data-preview-src="<?php echo e(strtok($previewUrl, '#')); ?>" <?php if($previewSection): ?> data-preview-hash="#<?php echo e(ltrim($previewSection, '#')); ?>" <?php endif; ?> title="Live preview" loading="eager"></iframe>
            </div>
        </div>

        <a href="<?php echo e($openUrl); ?>" target="_blank" data-preview-open class="btn-secondary w-full block text-center text-sm mt-2">Open in New Tab ↗</a>
    </div>
</aside>

<div class="xl:hidden mt-4 card p-3" data-builder-preview-mobile>
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-sm">Live Preview</h3>
        <button type="button" class="text-xs text-brand-600" data-preview-refresh>Refresh</button>
    </div>
    <div class="builder-preview-frame is-mobile" data-preview-frame>
        <iframe src="<?php echo e($previewUrl); ?>" data-theme-preview-iframe data-preview-src="<?php echo e(strtok($previewUrl, '#')); ?>" <?php if($previewSection): ?> data-preview-hash="#<?php echo e(ltrim($previewSection, '#')); ?>" <?php endif; ?> title="Live preview" loading="eager"></iframe>
    </div>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/admin/live-preview.blade.php ENDPATH**/ ?>