<?php if(isset($sections['newsletter'])): ?>
<section class="theme-section theme-newsletter">
    <div class="theme-container text-center">
        <h2 class="theme-section-title"><?php echo e($sections['newsletter']['settings']['title'] ?? 'Subscribe'); ?></h2>
        <p class="theme-newsletter-subtitle"><?php echo e($sections['newsletter']['settings']['subtitle'] ?? ''); ?></p>
        <form action="<?php echo e(route('newsletter.subscribe')); ?>" method="POST" class="theme-newsletter-form">
            <?php echo csrf_field(); ?>
            <input type="email" name="email" placeholder="Your email address" required class="theme-input">
            <button type="submit" class="theme-btn theme-btn-primary">Subscribe</button>
        </form>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/newsletter.blade.php ENDPATH**/ ?>