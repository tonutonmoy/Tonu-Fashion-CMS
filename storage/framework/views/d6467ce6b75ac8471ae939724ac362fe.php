<div id="shop-results-count" class="text-sm text-gray-500 mb-4">
    <?php echo e($products->total()); ?> product<?php echo e($products->total() === 1 ? '' : 's'); ?> found
</div>
<div id="shop-product-grid" class="theme-product-grid">
    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php echo $__env->make('themes.shared.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="col-span-full text-gray-500 py-12 text-center">No products match your filters.</p>
    <?php endif; ?>
</div>
<div id="shop-pagination" class="mt-8">
    <?php echo e($products->links()); ?>

</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/shop-products.blade.php ENDPATH**/ ?>