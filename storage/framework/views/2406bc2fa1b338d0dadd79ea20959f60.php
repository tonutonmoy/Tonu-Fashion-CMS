<footer id="site-footer" class="theme-footer theme-footer-<?php echo e($themeSettings->footer_style ?? 'default'); ?>">
    <div class="theme-container">
        <div class="theme-footer-grid">
            <div>
                <?php $footerLogo = $footerSettings->logo ?? $storeSettings['logo'] ?? null; ?>
                <?php if($footerLogo): ?>
                    <a href="<?php echo e(route('home')); ?>" class="inline-block">
                        <img src="<?php echo e(image_url($footerLogo)); ?>" alt="<?php echo e($storeSettings['name'] ?? 'Logo'); ?>" loading="lazy" decoding="async" width="180" height="56" class="theme-footer-logo">
                    </a>
                <?php else: ?>
                    <h3 class="theme-footer-brand"><?php echo e($storeSettings['name'] ?? 'Fashion Store'); ?></h3>
                <?php endif; ?>
                <?php if($footerSettings->description ?? null): ?>
                    <p class="theme-footer-text mt-2"><?php echo e($footerSettings->description); ?></p>
                <?php endif; ?>
                <p class="theme-footer-text"><?php echo e($footerSettings->address ?? $storeSettings['address']); ?></p>
            </div>
            <div>
                <h4 class="theme-footer-heading">Contact</h4>
                <?php if($footerSettings->phone ?? $storeSettings['phone'] ?? null): ?>
                    <p class="theme-footer-text">📞 <?php echo e($footerSettings->phone ?? $storeSettings['phone']); ?></p>
                <?php endif; ?>
                <?php if($footerSettings->email ?? $storeSettings['email'] ?? null): ?>
                    <p class="theme-footer-text">✉️ <?php echo e($footerSettings->email ?? $storeSettings['email']); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <h4 class="theme-footer-heading">Follow Us</h4>
                <div class="theme-footer-social">
                    <?php if($footerSettings->facebook_url ?? $storeSettings['facebook'] ?? null): ?>
                        <a href="<?php echo e($footerSettings->facebook_url ?? $storeSettings['facebook']); ?>" target="_blank">Facebook</a>
                    <?php endif; ?>
                    <?php if($footerSettings->instagram_url ?? $storeSettings['instagram'] ?? null): ?>
                        <a href="<?php echo e($footerSettings->instagram_url ?? $storeSettings['instagram']); ?>" target="_blank">Instagram</a>
                    <?php endif; ?>
                    <?php if($footerSettings->youtube_url ?? null): ?>
                        <a href="<?php echo e($footerSettings->youtube_url); ?>" target="_blank">YouTube</a>
                    <?php endif; ?>
                    <?php if($footerSettings->whatsapp_number ?? $storeSettings['whatsapp'] ?? null): ?>
                        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $footerSettings->whatsapp_number ?? $storeSettings['whatsapp'])); ?>" target="_blank">WhatsApp</a>
                    <?php endif; ?>
                    <?php if($footerSettings->messenger_link ?? $storeSettings['messenger'] ?? null): ?>
                        <a href="<?php echo e($footerSettings->messenger_link ?? $storeSettings['messenger']); ?>" target="_blank">Messenger</a>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h4 class="theme-footer-heading">Quick Links</h4>
                <div class="theme-footer-links">
                    <?php echo $__env->make('themes.shared.partials.menu-nav', ['items' => $footerMenu ?? collect(), 'class' => 'block'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <a href="<?php echo e(route('cart.index')); ?>">Cart</a>
                </div>
            </div>
        </div>
        <div class="theme-footer-bottom">
            <?php echo e($footerSettings->copyright_text ?? '© '.date('Y').' '.($storeSettings['name'] ?? config('app.name')).'. All rights reserved.'); ?>

        </div>
    </div>
</footer>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/footer.blade.php ENDPATH**/ ?>