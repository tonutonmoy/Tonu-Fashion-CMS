<?php
    $key = $sectionKey ?? 'featured_products';
    $products = $sections[$key]['products'] ?? collect();
    $title = match($key) {
        'new_arrivals' => 'New Arrivals',
        'best_sellers' => 'Best Sellers',
        default => 'Featured Products',
    };
?>
<?php if($products->isNotEmpty()): ?>
<section class="theme-section">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title"><?php echo e($title); ?></h2>
            <a href="<?php echo e(route('shop.index')); ?>" class="theme-link">View All</a>
        </div>
        <div class="theme-product-grid">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('themes.shared.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/products.blade.php ENDPATH**/ ?>