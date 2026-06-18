<?php if(isset($sections['flash_sale']) && ($sections['flash_sale']['active'] ?? false)): ?>
<?php $flash = $sections['flash_sale']; ?>
<section class="theme-section theme-flash-sale">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">⚡ Flash Sale — <?php echo e($flash['settings']['discount'] ?? 0); ?>% Off</h2>
            <?php if($flash['settings']['show_countdown'] ?? false): ?>
            <div class="theme-countdown" data-end="<?php echo e($flash['settings']['end_date']); ?>">
                <span class="theme-countdown-label">Ends in:</span>
                <span id="flash-countdown" class="theme-countdown-timer"></span>
            </div>
            <?php endif; ?>
        </div>
        <div class="theme-product-grid">
            <?php $__currentLoopData = $flash['products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('themes.shared.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/flash_sale.blade.php ENDPATH**/ ?>