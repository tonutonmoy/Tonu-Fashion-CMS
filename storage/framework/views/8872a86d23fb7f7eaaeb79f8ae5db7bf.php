<?php
    $images = $product->images->isNotEmpty()
        ? $product->images->sortByDesc('is_primary')->values()
        : collect();
    $activeVariants = $product->variants->where('status', 'active')->values();
    $variantPayload = $activeVariants->map(fn ($v) => [
        'id' => $v->id,
        'size' => $v->size,
        'color' => $v->color,
        'price_label' => format_bdt($v->price),
        'image' => $v->image ? image_url($v->image) : null,
        'stock' => $v->stock,
    ]);
?>

<div class="theme-container py-4 sm:py-8 pb-24 lg:pb-8">
    <nav class="theme-breadcrumb text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6 flex flex-wrap items-center gap-1 sm:gap-2">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-gray-900">Home</a>
        <span>/</span>
        <a href="<?php echo e(route('shop.index')); ?>" class="hover:text-gray-900">Shop</a>
        <?php if($product->category): ?>
            <span>/</span>
            <a href="<?php echo e(route('shop.index', ['category' => $product->category->slug])); ?>" class="hover:text-gray-900"><?php echo e($product->category->name); ?></a>
        <?php endif; ?>
        <span>/</span>
        <span class="text-gray-900"><?php echo e($product->name); ?></span>
    </nav>

    <p class="text-xs text-gray-400 mb-4">/<?php echo e($product->slug); ?></p>

    <div class="theme-product-detail">
        <div class="theme-product-gallery-wrap" data-product-gallery>
            <div class="theme-product-gallery-main relative overflow-hidden rounded-2xl bg-gray-100">
                <?php if($images->isNotEmpty()): ?>
                    <img
                        src="<?php echo e(image_url($images->first()->path)); ?>"
                        alt="<?php echo e($product->name); ?>"
                        class="theme-detail-image w-full aspect-[4/5] object-cover"
                        data-gallery-main
                        id="product-gallery-main"
                        width="800"
                        height="1000"
                        fetchpriority="high"
                        decoding="async"
                    >
                    <div class="theme-gallery-zoom hidden lg:block absolute top-0 left-[calc(100%+1rem)] w-[min(28rem,40vw)] h-full border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-xl z-10" data-gallery-zoom>
                        <div class="w-full h-full bg-no-repeat" data-gallery-zoom-inner></div>
                    </div>
                <?php else: ?>
                    <div class="theme-detail-placeholder aspect-[4/5] flex items-center justify-center">No Image</div>
                <?php endif; ?>
            </div>

            <?php if($images->count() > 1): ?>
                <div class="theme-product-thumbs flex gap-2 mt-3 overflow-x-auto">
                    <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button
                            type="button"
                            class="theme-gallery-thumb shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 <?php echo e($loop->first ? 'is-active border-gray-900' : 'border-transparent'); ?>"
                            data-gallery-thumb="<?php echo e(image_url($image->path)); ?>"
                            data-gallery-alt="<?php echo e($product->name); ?>"
                        >
                            <img src="<?php echo e(image_url($image->path)); ?>" alt="" class="w-full h-full object-cover">
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="theme-product-info">
            <?php if($product->category): ?>
                <a href="<?php echo e(route('shop.index', ['category' => $product->category->slug])); ?>" class="theme-product-category hover:underline"><?php echo e($product->category->name); ?></a>
            <?php endif; ?>
            <h1 class="theme-page-title"><?php echo e($product->name); ?></h1>

            <div class="theme-product-price">
                <span class="theme-price-current text-2xl" data-variant-price><?php echo e(format_bdt($product->effective_price)); ?></span>
                <?php if($product->isOnSale()): ?>
                    <span class="theme-price-old"><?php echo e(format_bdt($product->regular_price)); ?></span>
                <?php endif; ?>
            </div>

            <p class="theme-product-desc"><?php echo e($product->short_description); ?></p>

            <form action="<?php echo e(route('cart.store')); ?>" method="POST" class="theme-form space-y-4" data-add-to-cart id="product-add-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">

                <?php if($activeVariants->isNotEmpty()): ?>
                    <div class="space-y-4" data-product-variants data-variants='<?php echo json_encode($variantPayload, 15, 512) ?>'>
                        <?php if($activeVariants->pluck('size')->filter()->unique()->isNotEmpty()): ?>
                            <div>
                                <p class="text-sm font-medium mb-2">Size</p>
                                <div class="flex flex-wrap gap-2" data-size-group></div>
                            </div>
                        <?php endif; ?>
                        <?php if($activeVariants->pluck('color')->filter()->unique()->isNotEmpty()): ?>
                            <div>
                                <p class="text-sm font-medium mb-2">Color</p>
                                <div class="flex flex-wrap gap-2" data-color-group></div>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="product_variant_id" data-variant-id required>
                    </div>
                <?php endif; ?>

                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium">Qty</label>
                    <input type="number" name="quantity" value="1" min="1" max="99" class="theme-input w-24">
                </div>

                <div class="theme-product-actions grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="submit" class="theme-btn theme-btn-primary w-full min-h-[48px]" <?php if(!$product->inStock()): echo 'disabled'; endif; ?>>
                        <?php echo e($product->inStock() ? __('common.add_to_cart') : __('common.out_of_stock')); ?>

                    </button>
                    <button
                        type="button"
                        class="theme-btn theme-btn-outline w-full min-h-[48px]"
                        data-buy-now
                        data-checkout-url="<?php echo e(route('checkout.index')); ?>"
                        <?php if(!$product->inStock()): echo 'disabled'; endif; ?>
                    >
                        <?php echo e(__('common.checkout')); ?>

                    </button>
                </div>
            </form>

            <?php if(auth()->guard()->check()): ?>
                <form action="<?php echo e(route('wishlist.toggle', $product)); ?>" method="POST" class="mt-3"><?php echo csrf_field(); ?>
                    <button class="theme-btn theme-btn-outline w-full"><?php echo e($inWishlist ?? false ? '♥ In Wishlist' : '♡ Add to Wishlist'); ?></button>
                </form>
            <?php endif; ?>

            <div class="theme-product-full-desc mt-8"><?php echo nl2br(e($product->description)); ?></div>
        </div>
    </div>
</div>

<div class="mobile-atc-bar lg:hidden" data-mobile-atc>
    <div class="flex items-center gap-2 sm:gap-3">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold truncate"><?php echo e($product->name); ?></p>
            <p class="text-sm text-gray-600" data-variant-price><?php echo e(format_bdt($product->effective_price)); ?></p>
        </div>
        <button type="button" class="btn-secondary shrink-0 min-h-[44px] px-3 sm:px-4 text-sm" data-mobile-atc-add <?php if(!$product->inStock()): echo 'disabled'; endif; ?>>
            <?php echo e(__('common.add_to_cart')); ?>

        </button>
        <button
            type="button"
            class="btn-primary shrink-0 min-h-[44px] px-3 sm:px-4 text-sm"
            data-buy-now
            data-checkout-url="<?php echo e(route('checkout.index')); ?>"
            <?php if(!$product->inStock()): echo 'disabled'; endif; ?>
        >
            <?php echo e(__('common.checkout')); ?>

        </button>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    window.productVariants = <?php echo json_encode($variantPayload, 15, 512) ?>;
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/product-detail.blade.php ENDPATH**/ ?>