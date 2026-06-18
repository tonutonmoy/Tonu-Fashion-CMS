<?php $__env->startSection('content'); ?>
<div class="theme-container py-4 sm:py-8">
    <div class="theme-shop-layout">
        <div id="shop-filter-overlay" class="shop-filter-overlay md:hidden hidden" aria-hidden="true"></div>

        <aside id="shop-filter-panel" class="theme-shop-filters card p-4">
            <div class="flex items-center justify-between mb-4 md:hidden">
                <h3 class="font-semibold">Filters</h3>
                <button type="button" id="shop-filter-close" class="theme-icon-btn" aria-label="Close filters">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="shop-filter-form" method="GET" action="<?php echo e(route('shop.index')); ?>" class="space-y-5">
                <?php if(!empty($filters['q'])): ?>
                    <input type="hidden" name="q" value="<?php echo e($filters['q']); ?>">
                <?php endif; ?>

                <div class="hidden md:block">
                    <h3 class="font-semibold mb-3">Filters</h3>
                    <p class="text-xs text-gray-500">Updates automatically</p>
                </div>

                <div>
                    <label class="label">Category</label>
                    <select name="category" class="input shop-filter-input">
                        <option value="">All categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->slug); ?>" <?php if(($filters['category'] ?? '') === $cat->slug): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="label">Brand</label>
                    <select name="brand" class="input shop-filter-input">
                        <option value="">All brands</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->slug); ?>" <?php if(($filters['brand'] ?? '') === $brand->slug): echo 'selected'; endif; ?>><?php echo e($brand->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="label flex items-center gap-2">
                        <input type="checkbox" name="featured" value="1" class="shop-filter-input rounded" <?php if(!empty($filters['featured'])): echo 'checked'; endif; ?>>
                        Featured only
                    </label>
                </div>

                <div>
                    <label class="label">Price range (<?php echo e(config('fashion.currency_symbol', '৳')); ?>)</label>
                    <?php
                        $minBound = $priceBounds['min'];
                        $maxBound = $priceBounds['max'];
                        $curMin = (int) ($filters['min_price'] ?? $minBound);
                        $curMax = (int) ($filters['max_price'] ?? $maxBound);
                    ?>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span id="price-min-label"><?php echo e(format_bdt($curMin)); ?></span>
                        <span id="price-max-label"><?php echo e(format_bdt($curMax)); ?></span>
                    </div>
                    <div class="space-y-3">
                        <input type="range" id="price-min-slider" class="w-full shop-filter-input" min="<?php echo e($minBound); ?>" max="<?php echo e($maxBound); ?>" value="<?php echo e($curMin); ?>" step="50">
                        <input type="range" id="price-max-slider" class="w-full shop-filter-input" min="<?php echo e($minBound); ?>" max="<?php echo e($maxBound); ?>" value="<?php echo e($curMax); ?>" step="50">
                    </div>
                    <input type="hidden" name="min_price" id="min_price" value="<?php echo e($curMin); ?>">
                    <input type="hidden" name="max_price" id="max_price" value="<?php echo e($curMax); ?>">
                </div>

                <div>
                    <label class="label">Sort by</label>
                    <select name="sort" class="input shop-filter-input">
                        <option value="latest" <?php if(($filters['sort'] ?? 'latest') === 'latest'): echo 'selected'; endif; ?>>Latest</option>
                        <option value="price_asc" <?php if(($filters['sort'] ?? '') === 'price_asc'): echo 'selected'; endif; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php if(($filters['sort'] ?? '') === 'price_desc'): echo 'selected'; endif; ?>>Price: High to Low</option>
                        <option value="name" <?php if(($filters['sort'] ?? '') === 'name'): echo 'selected'; endif; ?>>Name A–Z</option>
                    </select>
                </div>

                <a href="<?php echo e(route('shop.index')); ?>" class="btn-secondary w-full text-center block">Clear filters</a>
            </form>
        </aside>

        <div class="theme-shop-results flex-1 min-w-0">
            <button type="button" id="shop-filter-toggle" class="shop-filter-toggle md:hidden mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M10 12h4"/></svg>
                Filters & Sort
            </button>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
                <h1 class="theme-page-title mb-0">
                    <?php if(!empty($filters['q'])): ?>
                        Search: “<?php echo e($filters['q']); ?>”
                    <?php else: ?>
                        Shop
                    <?php endif; ?>
                </h1>
            </div>
            <div id="shop-results">
                <?php echo $__env->make('themes.shared.partials.shop-products', ['products' => $products], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    window.shopPriceBounds = <?php echo json_encode($priceBounds, 15, 512) ?>;
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make(theme_layout(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/shop.blade.php ENDPATH**/ ?>