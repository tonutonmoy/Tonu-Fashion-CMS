<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['meta' => []]));

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

foreach (array_filter((['meta' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<title><?php echo e($meta['title'] ?? config('app.name')); ?></title>
<meta name="description" content="<?php echo e($meta['description'] ?? ''); ?>">
<link rel="canonical" href="<?php echo e($meta['canonical'] ?? url()->current()); ?>">
<meta property="og:title" content="<?php echo e($meta['title'] ?? config('app.name')); ?>">
<meta property="og:description" content="<?php echo e($meta['description'] ?? ''); ?>">
<meta property="og:type" content="<?php echo e($meta['og_type'] ?? 'website'); ?>">
<meta property="og:url" content="<?php echo e($meta['canonical'] ?? url()->current()); ?>">
<?php if(!empty($meta['og_image'])): ?>
<meta property="og:image" content="<?php echo e($meta['og_image']); ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo e($meta['title'] ?? config('app.name')); ?>">
<meta name="twitter:description" content="<?php echo e($meta['description'] ?? ''); ?>">
<?php if(!empty($meta['og_image'])): ?>
<meta name="twitter:image" content="<?php echo e($meta['og_image']); ?>">
<?php endif; ?>
<?php if(!empty($meta['twitter_handle'])): ?>
<meta name="twitter:site" content="<?php echo e($meta['twitter_handle']); ?>">
<?php endif; ?>
<?php if(!empty($meta['json_ld'])): ?>
    <?php $schemas = is_array($meta['json_ld']) && isset($meta['json_ld']['@context']) ? [$meta['json_ld']] : (array) $meta['json_ld']; ?>
    <?php $__currentLoopData = $schemas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schema): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_array($schema)): ?>
        <script type="application/ld+json"><?php echo json_encode(array_filter($schema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?></script>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/seo.blade.php ENDPATH**/ ?>