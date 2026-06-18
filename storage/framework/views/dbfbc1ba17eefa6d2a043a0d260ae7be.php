<?php
    $isActive = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p));
    $linkClass = fn (bool $active) => 'builder-nav-link'.($active ? ' is-active' : '');
?>

<div class="builder-nav builder-nav-compact">
    <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
        <div class="flex items-center gap-2 min-w-0">
            <button type="button" class="text-xs text-gray-500 hover:text-gray-800 flex items-center gap-1 shrink-0" data-builder-nav-toggle aria-expanded="true">
                <svg class="w-4 h-4 transition" data-builder-nav-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                <span>Menu</span>
            </button>
            <button type="button" class="text-xs text-gray-500 hover:text-gray-800 shrink-0 lg:hidden" data-admin-sidebar-open>☰ Admin</button>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <?php if($hasUnpublishedChanges ?? false): ?>
                <span class="builder-draft-badge" title="Changes are visible in Live Preview only">Unpublished draft</span>
            <?php endif; ?>
            <form action="<?php echo e(route('admin.builder.publish')); ?>" method="POST" class="inline" onsubmit="return confirm('Publish all draft changes to the live storefront?');">
                <?php echo csrf_field(); ?>
                <button type="submit" class="builder-publish-btn <?php echo e(($hasUnpublishedChanges ?? false) ? 'has-changes' : ''); ?>">
                    Publish
                </button>
            </form>
        </div>
    </div>

    <div class="builder-nav-body" data-builder-nav-body>
        <div class="builder-nav-scroll">
            <a href="<?php echo e(route('admin.builder.index')); ?>" class="<?php echo e($linkClass($isActive(['admin.builder.index']))); ?>">Overview</a>
            <span class="builder-nav-sep">|</span>
            <a href="<?php echo e(route('admin.theme.customizer')); ?>" class="<?php echo e($linkClass($isActive(['admin.theme.customizer']))); ?>">Theme</a>
            <a href="<?php echo e(route('admin.theme.homepage')); ?>" class="<?php echo e($linkClass($isActive(['admin.theme.homepage*']))); ?>">Homepage</a>
            <a href="<?php echo e(route('admin.theme.hero-slides')); ?>" class="<?php echo e($linkClass($isActive(['admin.theme.hero-slides*']))); ?>">Hero</a>
            <a href="<?php echo e(route('admin.theme.footer')); ?>" class="<?php echo e($linkClass($isActive(['admin.theme.footer']))); ?>">Footer</a>
            <a href="<?php echo e(route('admin.theme.seo')); ?>" class="<?php echo e($linkClass($isActive(['admin.theme.seo']))); ?>">SEO</a>
            <span class="builder-nav-sep">|</span>
            <a href="<?php echo e(route('admin.cms.menus.index')); ?>" class="<?php echo e($linkClass($isActive(['admin.cms.menus.*']))); ?>">Menus</a>
            <a href="<?php echo e(route('admin.cms.pages.index')); ?>" class="<?php echo e($linkClass($isActive(['admin.cms.pages.*']))); ?>">Pages</a>
            <a href="<?php echo e(route('admin.cms.blog.index')); ?>" class="<?php echo e($linkClass($isActive(['admin.cms.blog.*']))); ?>">Blog</a>
            <a href="<?php echo e(route('admin.cms.media.index')); ?>" class="<?php echo e($linkClass($isActive(['admin.cms.media.*']))); ?>">Media</a>
        </div>
        <p class="text-xs text-gray-500 mt-2">Save edits as draft — they appear in Live Preview only until you click <strong>Publish</strong>.</p>
    </div>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/builder/_nav.blade.php ENDPATH**/ ?>