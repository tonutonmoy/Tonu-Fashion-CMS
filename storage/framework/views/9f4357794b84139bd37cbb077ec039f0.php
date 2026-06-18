<?php if(session('marketing_add_to_cart')): ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.FashionMarketing) return;
    const data = <?php echo json_encode(session('marketing_add_to_cart'), 15, 512) ?>;
    FashionMarketing.addToCart(data.product, data.quantity, data.value);
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/marketing-flash.blade.php ENDPATH**/ ?>