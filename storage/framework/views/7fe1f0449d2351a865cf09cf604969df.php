<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product']));

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

foreach (array_filter((['product']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<a href="<?php echo e(route('products.show', $product->slug)); ?>" class="theme-product-card group">
    <div class="theme-product-image">
        <?php if($product->primary_image): ?>
            <img src="<?php echo e(image_url($product->primary_image)); ?>" alt="<?php echo e($product->name); ?>" loading="lazy" decoding="async" width="400" height="533" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm bg-gray-100">No Image</div>
        <?php endif; ?>
        <?php if($product->isOnSale()): ?>
            <span class="theme-badge-sale">Sale</span>
        <?php endif; ?>
    </div>
    <div class="theme-product-body">
        <p class="theme-product-category"><?php echo e($product->category?->name); ?></p>
        <h3 class="theme-product-title"><?php echo e(trans_field($product, 'name')); ?></h3>
        <div class="theme-product-price">
            <span class="theme-price-current"><?php echo e(format_bdt($product->effective_price)); ?></span>
            <?php if($product->isOnSale()): ?>
                <span class="theme-price-old"><?php echo e(format_bdt($product->regular_price)); ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/product-card.blade.php ENDPATH**/ ?>