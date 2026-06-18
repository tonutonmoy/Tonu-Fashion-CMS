<aside id="admin-sidebar" class="admin-sidebar w-64 bg-gray-900 text-gray-300 flex-shrink-0 fixed lg:static inset-y-0 left-0 z-[70] -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="p-6 border-b border-gray-800 flex items-center justify-between">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-white font-bold text-lg flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.icon','data' => ['name' => 'dashboard','class' => 'w-6 h-6 text-red-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'dashboard','class' => 'w-6 h-6 text-red-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd)): ?>
<?php $attributes = $__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd; ?>
<?php unset($__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd)): ?>
<?php $component = $__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd; ?>
<?php unset($__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd); ?>
<?php endif; ?>
            Admin Panel
        </a>
        <button type="button" id="admin-sidebar-close" class="lg:hidden text-gray-400 hover:text-white p-1" aria-label="Close sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="p-4 space-y-1 text-sm max-h-[calc(100vh-5rem)] overflow-y-auto">
        <?php
            $user = auth()->user();
            $nav = [
                ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'visible' => true],
                ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'customers', 'label' => 'Team Members', 'visible' => $user?->role->canManageUsers()],
                ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'products', 'label' => 'Products', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'categories', 'label' => 'Categories', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.brands.index', 'match' => 'admin.brands.*', 'icon' => 'brands', 'label' => 'Brands', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'icon' => 'orders', 'label' => 'Orders', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.support.index', 'match' => 'admin.support.*', 'icon' => 'support', 'label' => 'Support Chat', 'visible' => $user?->canAdmin('store'), 'badge' => true],
                ['route' => 'admin.cms.blog.index', 'match' => 'admin.cms.blog.*', 'icon' => 'blog', 'label' => 'Blog', 'visible' => $user?->canAdmin('blog')],
                ['route' => 'admin.cms.pages.index', 'match' => 'admin.cms.pages.*,admin.cms.menus.*,admin.cms.media.*', 'icon' => 'pages', 'label' => 'Pages & Media', 'visible' => $user?->canAdmin('cms')],
                ['route' => 'admin.payment.index', 'match' => 'admin.payment.*', 'icon' => 'payment', 'label' => 'Payment Gateways', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.courier.index', 'match' => 'admin.courier.*', 'icon' => 'courier', 'label' => 'Courier & Automation', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.customers.index', 'match' => 'admin.customers.*', 'icon' => 'customers', 'label' => 'Customers', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.coupons.index', 'match' => 'admin.coupons.*', 'icon' => 'coupons', 'label' => 'Coupons', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.reviews.index', 'match' => 'admin.reviews.*', 'icon' => 'reviews', 'label' => 'Reviews', 'visible' => $user?->canAdmin('store')],
                ['route' => 'admin.marketing.index', 'match' => 'admin.marketing.*', 'icon' => 'marketing', 'label' => 'Marketing & BD', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.builder.index', 'match' => 'admin.builder.*,admin.theme.*', 'icon' => 'builder', 'label' => 'Website Builder', 'visible' => $user?->canAdmin('settings')],
                ['route' => 'admin.settings.store', 'match' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'Settings', 'visible' => $user?->canAdmin('settings')],
            ];
        ?>
        <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($item['visible']): ?>
        <?php
            $patterns = explode(',', $item['match']);
            $active = collect($patterns)->contains(fn ($p) => request()->routeIs(trim($p)));
        ?>
        <a href="<?php echo e(route($item['route'])); ?>" class="admin-nav-link <?php echo e($active ? 'is-active' : ''); ?>">
            <?php if (isset($component)) { $__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.icon','data' => ['name' => $item['icon'],'class' => 'w-5 h-5 shrink-0 '.e($active ? 'text-red-400' : 'text-gray-400').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon']),'class' => 'w-5 h-5 shrink-0 '.e($active ? 'text-red-400' : 'text-gray-400').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd)): ?>
<?php $attributes = $__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd; ?>
<?php unset($__attributesOriginal906aaa6a63a2f5f8b29c23c3195c96dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd)): ?>
<?php $component = $__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd; ?>
<?php unset($__componentOriginal906aaa6a63a2f5f8b29c23c3195c96dd); ?>
<?php endif; ?>
            <span class="flex-1"><?php echo e($item['label']); ?></span>
            <?php if(!empty($item['badge'])): ?>
            <span id="admin-support-nav-badge" class="hidden min-w-[1.25rem] h-5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">0</span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
</aside>
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/40 z-[60] hidden lg:hidden" aria-hidden="true"></div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/partials/admin/sidebar.blade.php ENDPATH**/ ?>