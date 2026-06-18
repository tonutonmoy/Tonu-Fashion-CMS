<div class="mb-6 flex flex-wrap gap-4 border-b border-gray-200 pb-4 text-sm">
    <a href="<?php echo e(route('admin.marketing.index')); ?>" class="<?php echo e(request()->routeIs('admin.marketing.index') ? 'font-semibold text-brand-600' : 'text-gray-600'); ?>">Pixels & CAPI</a>
    <a href="<?php echo e(route('admin.marketing.shipping')); ?>" class="<?php echo e(request()->routeIs('admin.marketing.shipping') ? 'font-semibold text-brand-600' : 'text-gray-600'); ?>">Shipping (BD)</a>
    <a href="<?php echo e(route('admin.marketing.sms')); ?>" class="<?php echo e(request()->routeIs('admin.marketing.sms') ? 'font-semibold text-brand-600' : 'text-gray-600'); ?>">SMS</a>
    <a href="<?php echo e(route('admin.marketing.social-chat')); ?>" class="<?php echo e(request()->routeIs('admin.marketing.social-chat') ? 'font-semibold text-brand-600' : 'text-gray-600'); ?>">Social Chat</a>
    <a href="<?php echo e(route('admin.marketing.seo')); ?>" class="<?php echo e(request()->routeIs('admin.marketing.seo') ? 'font-semibold text-brand-600' : 'text-gray-600'); ?>">SEO</a>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/marketing/_nav.blade.php ENDPATH**/ ?>