<?php if($relatedProducts->isNotEmpty()): ?>
<section class="theme-section">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">You may also like</h2>
            <a href="<?php echo e(route('shop.index', ['category' => $product->category?->slug])); ?>" class="theme-link">View more</a>
        </div>
        <div class="theme-product-grid">
            <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('themes.shared.product-card', ['product' => $related], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/related-products.blade.php ENDPATH**/ ?>