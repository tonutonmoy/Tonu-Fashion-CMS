<?php $product = $product ?? null; ?>
<div class="card p-6 space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="label">Name *</label>
            <input type="text" name="name" value="<?php echo e(old('name', $product?->name)); ?>" class="input" required data-slug-source data-slug-target="#product-slug">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Slug</label>
            <input type="text" name="slug" id="product-slug" value="<?php echo e(old('slug', $product?->slug)); ?>" class="input" placeholder="auto-generated-from-name" data-manual="<?php echo e(old('slug', $product?->slug) ? 'true' : 'false'); ?>">
            <p class="text-xs text-gray-500 mt-1">URL: /products/<span id="product-slug-preview"><?php echo e(old('slug', $product?->slug ?: 'your-product')); ?></span></p>
        </div>
        <div>
            <label class="label">SKU *</label>
            <input type="text" name="sku" value="<?php echo e(old('sku', $product?->sku)); ?>" class="input" required>
        </div>
        <div>
            <label class="label">Category *</label>
            <select name="category_id" class="input" required>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php if(old('category_id', $product?->category_id) == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Brand</label>
            <select name="brand_id" class="input">
                <option value="">None</option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($brand->id); ?>" <?php if(old('brand_id', $product?->brand_id) == $brand->id): echo 'selected'; endif; ?>><?php echo e($brand->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Regular Price *</label>
            <input type="number" name="regular_price" step="0.01" value="<?php echo e(old('regular_price', $product?->regular_price)); ?>" class="input" required>
        </div>
        <div>
            <label class="label">Sale Price</label>
            <input type="number" name="sale_price" step="0.01" value="<?php echo e(old('sale_price', $product?->sale_price)); ?>" class="input">
        </div>
        <div>
            <label class="label">Stock *</label>
            <input type="number" name="stock" value="<?php echo e(old('stock', $product?->stock ?? 0)); ?>" class="input" required>
        </div>
        <div>
            <label class="label">Status *</label>
            <select name="status" class="input" required>
                <option value="active" <?php if(old('status', $product?->status?->value ?? 'active') === 'active'): echo 'selected'; endif; ?>>Active</option>
                <option value="inactive" <?php if(old('status', $product?->status?->value) === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
            </select>
        </div>
        <div class="sm:col-span-2 flex flex-wrap gap-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="featured" value="1" <?php if(old('featured', $product?->featured)): echo 'checked'; endif; ?> class="rounded">
                <span class="text-sm">Featured Product</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="free_delivery" value="1" <?php if(old('free_delivery', $product?->free_delivery)): echo 'checked'; endif; ?> class="rounded">
                <span class="text-sm">Free Delivery</span>
            </label>
        </div>
        <div class="sm:col-span-2">
            <label class="label">Short Description</label>
            <textarea name="short_description" rows="2" class="input"><?php echo e(old('short_description', $product?->short_description)); ?></textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="label">Description</label>
            <textarea name="description" rows="5" class="input"><?php echo e(old('description', $product?->description)); ?></textarea>
        </div>
        <div class="sm:col-span-2">
                <?php if (isset($component)) { $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.image-uploader','data' => ['name' => 'images','label' => 'Product Images','multiple' => true,'existing' => $product?->images ?? [],'primaryName' => 'primary_image_id','primaryValue' => old('primary_image_id', $product?->images->firstWhere('is_primary')?->id),'hint' => 'First image can be set as main · drag multiple files at once']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.image-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'images','label' => 'Product Images','multiple' => true,'existing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product?->images ?? []),'primary-name' => 'primary_image_id','primary-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('primary_image_id', $product?->images->firstWhere('is_primary')?->id)),'hint' => 'First image can be set as main · drag multiple files at once']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d)): ?>
<?php $attributes = $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d; ?>
<?php unset($__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0703b2d66d34e0cfa15c96063b3d553d)): ?>
<?php $component = $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d; ?>
<?php unset($__componentOriginal0703b2d66d34e0cfa15c96063b3d553d); ?>
<?php endif; ?>
        </div>
        <div>
            <label class="label">Meta Title</label>
            <input type="text" name="meta_title" value="<?php echo e(old('meta_title', $product?->meta_title)); ?>" class="input">
        </div>
        <div>
            <label class="label">Meta Description</label>
            <input type="text" name="meta_description" value="<?php echo e(old('meta_description', $product?->meta_description)); ?>" class="input">
        </div>
    </div>
</div>

<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold">Variants (Size & Color)</h3>
        <button type="button" id="add-variant-row" class="btn-secondary text-sm">+ Add Variant</button>
    </div>
  <div id="variants" class="space-y-4" data-sizes='<?php echo json_encode($sizes, 15, 512) ?>' data-colors='<?php echo json_encode($colors, 15, 512) ?>'>
        <?php $variants = old('variants', $product?->variants?->toArray() ?? [['size' => 'M', 'color' => 'Black', 'stock' => 10]]); ?>
        <?php $__currentLoopData = $variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border border-gray-200 rounded-xl p-4 space-y-3" data-variant-row>
            <div class="flex justify-between items-center">
                <p class="text-sm font-medium">Variant #<?php echo e($i + 1); ?></p>
                <button type="button" class="text-red-600 text-sm" data-remove-variant>Remove</button>
            </div>
            <?php if(!empty($variant['id'])): ?>
                <input type="hidden" name="variants[<?php echo e($i); ?>][id]" value="<?php echo e($variant['id']); ?>">
            <?php endif; ?>
            <input type="hidden" name="variants[<?php echo e($i); ?>][size]" data-size-input value="<?php echo e($variant['size'] ?? ''); ?>">
            <input type="hidden" name="variants[<?php echo e($i); ?>][color]" data-color-input value="<?php echo e($variant['color'] ?? ''); ?>">
            <div>
                <p class="text-xs text-gray-500 mb-2">Size</p>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="variant-chip <?php echo e(($variant['size'] ?? '') === $size ? 'is-active' : ''); ?>" data-variant-size="<?php echo e($size); ?>"><?php echo e($size); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-2">Color</p>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="variant-chip <?php echo e(($variant['color'] ?? '') === $color ? 'is-active' : ''); ?>" data-variant-color="<?php echo e($color); ?>"><?php echo e($color); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input type="number" name="variants[<?php echo e($i); ?>][stock]" value="<?php echo e($variant['stock'] ?? 0); ?>" placeholder="Stock" class="input">
                <input type="number" name="variants[<?php echo e($i); ?>][price_adjustment]" value="<?php echo e($variant['price_adjustment'] ?? 0); ?>" step="0.01" placeholder="Price +/-" class="input">
            </div>
            <?php if(!empty($variant['image'])): ?>
                <div class="flex items-center gap-3">
                    <img src="<?php echo e(image_url($variant['image'])); ?>" alt="" class="w-16 h-16 rounded-lg object-cover border">
                    <label class="text-sm flex items-center gap-2"><input type="checkbox" name="variants[<?php echo e($i); ?>][remove_image]" value="1"> Remove image</label>
                </div>
            <?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.image-uploader','data' => ['name' => 'variants['.e($i).'][image]','label' => 'Variant Image','multiple' => false,'hint' => 'Optional · overrides product image for this variant']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.image-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'variants['.e($i).'][image]','label' => 'Variant Image','multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'hint' => 'Optional · overrides product image for this variant']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d)): ?>
<?php $attributes = $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d; ?>
<?php unset($__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0703b2d66d34e0cfa15c96063b3d553d)): ?>
<?php $component = $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d; ?>
<?php unset($__componentOriginal0703b2d66d34e0cfa15c96063b3d553d); ?>
<?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/products/_form.blade.php ENDPATH**/ ?>