<?php if(!empty($marketingProduct)): ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.FashionMarketing) {
        FashionMarketing.viewContent(<?php echo json_encode($marketingProduct, 15, 512) ?>);
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/product-marketing.blade.php ENDPATH**/ ?>