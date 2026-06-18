<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> | <?php echo e($storeSettings['name'] ?? config('app.name')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/admin-entry.js']); ?>
</head>
<body class="admin-body bg-gray-100 min-h-screen theme-mode-<?php echo e($colorMode ?? 'light'); ?>" data-color-mode="<?php echo e($colorMode ?? 'light'); ?>" data-admin-support-notify>
    <?php if (isset($component)) { $__componentOriginal30b09ba64d8f9e6b0023e860875d7bb6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30b09ba64d8f9e6b0023e860875d7bb6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30b09ba64d8f9e6b0023e860875d7bb6)): ?>
<?php $attributes = $__attributesOriginal30b09ba64d8f9e6b0023e860875d7bb6; ?>
<?php unset($__attributesOriginal30b09ba64d8f9e6b0023e860875d7bb6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30b09ba64d8f9e6b0023e860875d7bb6)): ?>
<?php $component = $__componentOriginal30b09ba64d8f9e6b0023e860875d7bb6; ?>
<?php unset($__componentOriginal30b09ba64d8f9e6b0023e860875d7bb6); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal198527e2b6c4fdc504e2b537e7367e9a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal198527e2b6c4fdc504e2b537e7367e9a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.confirm-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.confirm-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal198527e2b6c4fdc504e2b537e7367e9a)): ?>
<?php $attributes = $__attributesOriginal198527e2b6c4fdc504e2b537e7367e9a; ?>
<?php unset($__attributesOriginal198527e2b6c4fdc504e2b537e7367e9a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal198527e2b6c4fdc504e2b537e7367e9a)): ?>
<?php $component = $__componentOriginal198527e2b6c4fdc504e2b537e7367e9a; ?>
<?php unset($__componentOriginal198527e2b6c4fdc504e2b537e7367e9a); ?>
<?php endif; ?>
    <div id="admin-loading" class="hidden fixed inset-0 z-[200] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm" aria-live="polite" aria-busy="true">
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 flex flex-col items-center gap-3 min-w-[12rem]">
            <div class="w-10 h-10 border-4 border-red-100 border-t-red-600 rounded-full animate-spin" role="status"></div>
            <p id="admin-loading-text" class="text-sm font-medium text-gray-700">Please wait…</p>
        </div>
    </div>
    <div class="flex min-h-screen">
        <?php echo $__env->make('partials.admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div id="admin-main-wrap" class="flex-1 flex flex-col min-w-0">
            <?php echo $__env->make('partials.admin.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/layouts/admin.blade.php ENDPATH**/ ?>