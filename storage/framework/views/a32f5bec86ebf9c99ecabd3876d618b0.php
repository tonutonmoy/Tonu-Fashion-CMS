<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'preview' => true,
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
    'preview' => true,
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
    $previewUrl = $previewUrl ?? route('home');
    $openUrl = $openUrl ?? $previewUrl;
?>

<div class="builder-shell <?php echo e($preview ? 'builder-shell-with-preview' : ''); ?>" data-builder-shell>
    <?php echo $__env->make('admin.builder._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="builder-shell-grid">
        <div class="builder-shell-main min-w-0">
            <?php echo e($slot); ?>

        </div>

        <?php if($preview): ?>
            <?php if (isset($component)) { $__componentOriginal094538268f86def3c10491eddbb29fa2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal094538268f86def3c10491eddbb29fa2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.live-preview','data' => ['live' => $live,'settings' => $settings,'previewUrl' => $previewUrl,'openUrl' => $openUrl,'previewLabel' => $previewLabel,'previewSection' => $previewSection]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.live-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['live' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($live),'settings' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($settings),'preview-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($previewUrl),'open-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($openUrl),'preview-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($previewLabel),'preview-section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($previewSection)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal094538268f86def3c10491eddbb29fa2)): ?>
<?php $attributes = $__attributesOriginal094538268f86def3c10491eddbb29fa2; ?>
<?php unset($__attributesOriginal094538268f86def3c10491eddbb29fa2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal094538268f86def3c10491eddbb29fa2)): ?>
<?php $component = $__componentOriginal094538268f86def3c10491eddbb29fa2; ?>
<?php unset($__componentOriginal094538268f86def3c10491eddbb29fa2); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/admin/builder-layout.blade.php ENDPATH**/ ?>