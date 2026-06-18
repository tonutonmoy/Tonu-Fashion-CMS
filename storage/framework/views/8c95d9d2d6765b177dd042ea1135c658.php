<?php if(isset($sections['categories']) && ($sections['categories']['categories'] ?? collect())->isNotEmpty()): ?>
<?php
    $categoryItems = $sections['categories']['categories'];
    $limit = (int) ($sections['categories']['settings']['limit'] ?? 6);
    $cols = min(max($categoryItems->count(), 1), $limit);
    $colsMd = min($cols, 6);
?>
<section class="theme-section">
    <div class="theme-container">
        <h2 class="theme-section-title">Shop by Category</h2>
        <div class="theme-category-grid" style="--category-cols: <?php echo e($colsMd); ?>;">
            <?php $__currentLoopData = $categoryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('categories.show', $category)); ?>" class="theme-category-card">
                <?php if($category->image): ?>
                    <img src="<?php echo e(image_url($category->image)); ?>" alt="<?php echo e($category->name); ?>" loading="lazy" decoding="async" class="theme-category-img">
                <?php endif; ?>
                <span><?php echo e($category->name); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/categories.blade.php ENDPATH**/ ?>