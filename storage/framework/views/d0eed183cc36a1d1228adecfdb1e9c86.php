<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['theme' => $activeTheme ?? 'fashion-modern']));

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

foreach (array_filter((['theme' => $activeTheme ?? 'fashion-modern']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($storeSettings['favicon'] ?? null): ?>
    <link rel="icon" href="<?php echo e(image_url($storeSettings['favicon'])); ?>">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="<?php echo e(theme()->googleFontUrl()); ?>" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="<?php echo e(theme()->googleFontUrl()); ?>" rel="stylesheet"></noscript>
<style><?php echo theme()->cssVariables(); ?></style>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/storefront.js']); ?>
<link rel="stylesheet" href="<?php echo e(theme_asset('theme.css')); ?>">
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/head.blade.php ENDPATH**/ ?>