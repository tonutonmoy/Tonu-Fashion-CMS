<?php $__env->startSection('content'); ?>
<div class="theme-container py-8 sm:py-12">
    <?php if($page->banner_image): ?>
        <img src="<?php echo e(image_url($page->banner_image)); ?>" alt="<?php echo e(trans_field($page, 'title')); ?>" class="w-full h-48 sm:h-64 object-cover rounded-2xl mb-8" loading="eager">
    <?php endif; ?>
    <h1 class="theme-page-title mb-6"><?php echo e(trans_field($page, 'title')); ?></h1>
    <div class="prose max-w-none cms-content"><?php echo trans_field($page, 'content'); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(theme_layout(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/page.blade.php ENDPATH**/ ?>