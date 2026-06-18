<?php $__env->startSection('content'); ?>
<div class="theme-container">
    <div class="checkout-page">
        <h1 class="checkout-page-title"><?php echo e(__('common.checkout')); ?></h1>
        <?php echo $__env->make('themes.shared.partials.checkout-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('themes.fashion-modern.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/fashion-modern/checkout.blade.php ENDPATH**/ ?>