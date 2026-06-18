<header class="theme-header theme-header-<?php echo e($themeSettings->header_style ?? 'default'); ?>">
    <div class="theme-container theme-header-inner">
        <div class="theme-header-top">
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" id="mobile-menu-toggle" class="theme-icon-btn lg:hidden" aria-label="<?php echo e(__('common.menu')); ?>" aria-expanded="false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="<?php echo e(route('home')); ?>" class="theme-logo">
                    <?php if($storeSettings['logo'] ?? null): ?>
                        <img src="<?php echo e(image_url($storeSettings['logo'])); ?>" alt="<?php echo e($storeSettings['name']); ?>" loading="eager" decoding="async" width="160" height="48" class="theme-logo-img">
                    <?php else: ?>
                        <span class="theme-logo-text"><?php echo e($storeSettings['name'] ?? 'Fashion Store'); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="theme-header-search hidden md:flex flex-1 max-w-md mx-4 lg:mx-6 relative" data-header-search>
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    type="search"
                    name="q"
                    value="<?php echo e(request('q')); ?>"
                    placeholder="<?php echo e(__('common.search_products')); ?>"
                    class="input w-full pl-10 rounded-full border-gray-200 bg-gray-50 focus:bg-white"
                    aria-label="<?php echo e(__('common.search_products')); ?>"
                >
            </form>

            <div class="theme-header-actions flex items-center gap-1 shrink-0 lg:hidden">
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
                <button type="button" data-open-cart class="theme-cart-btn relative theme-icon-btn" aria-label="<?php echo e(__('common.cart')); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span data-cart-count class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center <?php echo e(($cartCount ?? 0) ? '' : 'hidden'); ?>"><?php echo e($cartCount ?? 0); ?></span>
                </button>
            </div>
        </div>

        <nav class="theme-nav hidden lg:flex items-center gap-1 xl:gap-2 flex-1 min-w-0 justify-end">
            <?php echo $__env->make('themes.shared.partials.menu-nav', ['items' => $headerMenu ?? collect()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <a href="<?php echo e(route('wishlist.index')); ?>" class="theme-nav-link shrink-0"><?php echo e(__('common.wishlist')); ?></a>
            <div class="theme-header-actions flex items-center gap-1 shrink-0 ml-2 pl-2 border-l border-gray-200">
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
                <button type="button" data-open-cart class="theme-cart-btn relative theme-icon-btn" aria-label="<?php echo e(__('common.cart')); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span data-cart-count class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center <?php echo e(($cartCount ?? 0) ? '' : 'hidden'); ?>"><?php echo e($cartCount ?? 0); ?></span>
                </button>
            </div>
        </nav>
    </div>

    <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="theme-container pb-3 md:hidden px-3 theme-header-search-mobile" data-header-search>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(__('common.search_products')); ?>" class="input w-full pl-10 rounded-full min-h-[44px]" aria-label="<?php echo e(__('common.search_products')); ?>">
        </div>
    </form>
</header>

<div id="mobile-menu-overlay" class="fixed inset-0 bg-black/40 z-[80] hidden lg:hidden" aria-hidden="true"></div>
<nav id="mobile-menu" class="fixed top-0 left-0 h-full w-[min(20rem,85vw)] bg-white z-[90] shadow-2xl transform translate-x-full transition-transform duration-300 lg:hidden flex flex-col" aria-label="Mobile navigation">
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <span class="font-semibold"><?php echo e($storeSettings['name'] ?? __('common.menu')); ?></span>
        <button type="button" id="mobile-menu-close" class="theme-icon-btn" aria-label="<?php echo e(__('common.close')); ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-4 space-y-1 overflow-y-auto flex-1">
        <a href="<?php echo e(route('home')); ?>" class="mobile-nav-link"><?php echo e(__('common.home')); ?></a>
        <?php echo $__env->make('themes.shared.partials.menu-nav', ['items' => $headerMenu ?? collect(), 'class' => 'mobile-nav-link block'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <a href="<?php echo e(route('wishlist.index')); ?>" class="mobile-nav-link"><?php echo e(__('common.wishlist')); ?></a>
    </div>
    <div class="p-4 border-t border-gray-200 flex items-center justify-end gap-2">
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
        <button type="button" data-open-cart class="theme-cart-btn relative theme-icon-btn" aria-label="<?php echo e(__('common.cart')); ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span data-cart-count class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-brand-600 text-white text-xs flex items-center justify-center <?php echo e(($cartCount ?? 0) ? '' : 'hidden'); ?>"><?php echo e($cartCount ?? 0); ?></span>
        </button>
    </div>
</nav>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/header.blade.php ENDPATH**/ ?>