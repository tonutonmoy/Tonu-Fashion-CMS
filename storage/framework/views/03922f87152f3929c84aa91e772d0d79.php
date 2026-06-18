<header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <button type="button" id="admin-sidebar-toggle" class="admin-sidebar-toggle-btn theme-icon-btn shrink-0" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-base sm:text-lg font-semibold text-gray-900 truncate"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
    </div>
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <?php if (isset($component)) { $__componentOriginal1ecea76caa6fd571667f2c33040a0355 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1ecea76caa6fd571667f2c33040a0355 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.color-mode-toggle','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('color-mode-toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1ecea76caa6fd571667f2c33040a0355)): ?>
<?php $attributes = $__attributesOriginal1ecea76caa6fd571667f2c33040a0355; ?>
<?php unset($__attributesOriginal1ecea76caa6fd571667f2c33040a0355); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1ecea76caa6fd571667f2c33040a0355)): ?>
<?php $component = $__componentOriginal1ecea76caa6fd571667f2c33040a0355; ?>
<?php unset($__componentOriginal1ecea76caa6fd571667f2c33040a0355); ?>
<?php endif; ?>
        <a href="<?php echo e(route('home')); ?>" target="_blank" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900 hidden sm:inline"><?php echo e(__('admin.view_store')); ?></a>
        <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-700"><?php echo e(__('common.logout')); ?></button></form>
    </div>
</header>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/partials/admin/header.blade.php ENDPATH**/ ?>