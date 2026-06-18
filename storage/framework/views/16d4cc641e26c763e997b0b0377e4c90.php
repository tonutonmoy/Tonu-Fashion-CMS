<?php $__env->startSection('title', 'Payment Gateways'); ?>
<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('admin.payment.index')); ?>" method="POST" class="max-w-3xl space-y-8">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="card p-6">
        <label class="flex items-center gap-2 font-semibold">
            <input type="checkbox" name="cod_enabled" value="1" <?php if($settings['cod_enabled'] ?? true): echo 'checked'; endif; ?>>
            Cash on Delivery (COD)
        </label>
    </div>

    <?php $__currentLoopData = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'sslcommerz' => 'SSLCommerz']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $g = $settings[$key] ?? [] ?>
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg"><?php echo e($label); ?></h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="<?php echo e($key); ?>_enabled" value="1" <?php if($g['enabled'] ?? false): echo 'checked'; endif; ?>> Enable
            </label>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="<?php echo e($key); ?>_sandbox" value="1" <?php if($g['sandbox'] ?? true): echo 'checked'; endif; ?>> Sandbox mode
        </label>
        <?php if($key === 'sslcommerz'): ?>
        <div><label class="label">Store ID</label><input name="<?php echo e($key); ?>_store_id" value="<?php echo e($g['store_id'] ?? ''); ?>" class="input"></div>
        <div><label class="label">Store Password</label><input name="<?php echo e($key); ?>_app_secret" type="password" value="<?php echo e($g['app_secret'] ?? ''); ?>" class="input"></div>
        <?php elseif($key === 'nagad'): ?>
        <div><label class="label">Merchant ID</label><input name="<?php echo e($key); ?>_merchant_id" value="<?php echo e($g['merchant_id'] ?? ''); ?>" class="input"></div>
        <div><label class="label">Public Key (base64)</label><input name="<?php echo e($key); ?>_app_key" value="<?php echo e($g['app_key'] ?? ''); ?>" class="input"></div>
        <div><label class="label">Private Key (base64)</label><input name="<?php echo e($key); ?>_app_secret" type="password" value="<?php echo e($g['app_secret'] ?? ''); ?>" class="input"></div>
        <?php else: ?>
        <div><label class="label">App Key</label><input name="<?php echo e($key); ?>_app_key" value="<?php echo e($g['app_key'] ?? ''); ?>" class="input"></div>
        <div><label class="label">App Secret</label><input name="<?php echo e($key); ?>_app_secret" type="password" value="<?php echo e($g['app_secret'] ?? ''); ?>" class="input"></div>
        <div><label class="label">Username</label><input name="<?php echo e($key); ?>_username" value="<?php echo e($g['username'] ?? ''); ?>" class="input"></div>
        <div><label class="label">Password</label><input name="<?php echo e($key); ?>_password" type="password" value="<?php echo e($g['password'] ?? ''); ?>" class="input"></div>
        <?php endif; ?>
        <div><label class="label">Base URL (optional override)</label><input name="<?php echo e($key); ?>_base_url" value="<?php echo e($g['base_url'] ?? ''); ?>" class="input" placeholder="<?php echo e(config('payments.'.$key.'.sandbox_url')); ?>"></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <button class="btn-primary">Save Payment Settings</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/payment/index.blade.php ENDPATH**/ ?>