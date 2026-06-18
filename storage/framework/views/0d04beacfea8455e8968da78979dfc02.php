<?php if(isset($sections['customer_reviews']) && ($sections['customer_reviews']['reviews'] ?? collect())->isNotEmpty()): ?>
<section class="theme-section theme-reviews">
    <div class="theme-container">
        <h2 class="theme-section-title">Customer Reviews</h2>
        <div class="theme-review-grid">
            <?php $__currentLoopData = $sections['customer_reviews']['reviews']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="theme-review-card">
                <div class="theme-review-stars"><?php echo e(str_repeat('★', $review->rating)); ?></div>
                <p class="theme-review-text"><?php echo e($review->comment); ?></p>
                <p class="theme-review-author">— <?php echo e($review->user?->name); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/customer_reviews.blade.php ENDPATH**/ ?>