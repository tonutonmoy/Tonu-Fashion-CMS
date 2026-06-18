<div id="admin-toast-root" class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none"></div>

<?php if(session('success')): ?>
    <div data-admin-toast data-type="success" class="hidden"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div data-admin-toast data-type="error" class="hidden"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('status')): ?>
    <div data-admin-toast data-type="info" class="hidden"><?php echo e(session('status')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div data-admin-toast data-type="error" class="hidden"><?php echo e($errors->first()); ?></div>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/admin/toast.blade.php ENDPATH**/ ?>