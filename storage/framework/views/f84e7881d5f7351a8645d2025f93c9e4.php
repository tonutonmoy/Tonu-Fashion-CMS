<?php $__env->startSection('title', 'Add Product'); ?>

<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('admin.products._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button type="submit" class="btn-primary">Create Product</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/products/create.blade.php ENDPATH**/ ?>