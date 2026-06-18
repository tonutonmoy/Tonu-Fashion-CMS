<?php $__env->startSection('title', 'Homepage Builder'); ?>
<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.builder-layout','data' => ['previewUrl' => builder_preview_url(),'previewLabel' => 'Homepage — all sections']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.builder-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(builder_preview_url()),'preview-label' => 'Homepage — all sections']); ?>

<form action="<?php echo e(route('admin.theme.homepage.reorder')); ?>" method="POST" class="mb-6" data-homepage-reorder-form>
    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
    <button type="submit" class="btn-secondary">Save Section Order</button>
</form>

<div class="space-y-3" data-homepage-sort>
    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card p-6" data-section-id="<?php echo e($section->id); ?>">
        <div class="flex items-center gap-3 mb-4 cursor-grab">
            <span class="text-gray-400 text-lg" title="Drag to reorder">⠿</span>
            <form action="<?php echo e(route('admin.theme.homepage.toggle', $section)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <input type="hidden" name="enabled" value="<?php echo e($section->enabled ? '0' : '1'); ?>">
                <button type="submit" class="badge <?php echo e($section->enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'); ?>">
                    <?php echo e($section->enabled ? 'Enabled' : 'Disabled'); ?>

                </button>
            </form>
            <h3 class="font-semibold flex-1"><?php echo e($section->title); ?></h3>
            <span class="text-xs text-gray-400">#<?php echo e($section->sort_order); ?></span>
        </div>
        <form action="<?php echo e(route('admin.theme.homepage.update', $section)); ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php if(in_array($section->section_key, ['categories', 'featured_products', 'new_arrivals', 'best_sellers', 'customer_reviews'])): ?>
            <div><label class="label">Limit</label><input type="number" name="settings[limit]" value="<?php echo e($section->settings['limit'] ?? 6); ?>" class="input" min="1" max="24"></div>
            <?php endif; ?>
            <?php if($section->section_key === 'categories'): ?>
            <div class="md:col-span-2"><label class="label">Categories (leave empty for all)</label>
                <select name="settings[category_ids][]" multiple class="input h-32">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cat->id); ?>" <?php if(in_array($cat->id, $section->settings['category_ids'] ?? [])): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if($section->section_key === 'featured_products'): ?>
            <div class="md:col-span-2"><label class="label">Products (leave empty for auto featured)</label>
                <select name="settings[product_ids][]" multiple class="input h-32">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if(in_array($p->id, $section->settings['product_ids'] ?? [])): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if($section->section_key === 'flash_sale'): ?>
            <div><label class="label">Limit</label><input type="number" name="settings[limit]" value="<?php echo e($section->settings['limit'] ?? 8); ?>" class="input" min="1" max="24"></div>
            <div><label class="label">Start Date</label><input type="date" name="settings[start_date]" value="<?php echo e($section->settings['start_date'] ?? ''); ?>" class="input"></div>
            <div><label class="label">End Date</label><input type="date" name="settings[end_date]" value="<?php echo e($section->settings['end_date'] ?? ''); ?>" class="input"></div>
            <div><label class="label">Discount %</label><input type="number" name="settings[discount]" value="<?php echo e($section->settings['discount'] ?? 20); ?>" class="input"></div>
            <div><label class="label flex items-center gap-2 mt-6"><input type="checkbox" name="settings[show_countdown]" value="1" <?php if($section->settings['show_countdown'] ?? true): echo 'checked'; endif; ?>> Show Countdown</label></div>
            <?php endif; ?>
            <?php if($section->section_key === 'newsletter'): ?>
            <div><label class="label">Title</label><input name="settings[title]" value="<?php echo e($section->settings['title'] ?? ''); ?>" class="input"></div>
            <div><label class="label">Subtitle</label><input name="settings[subtitle]" value="<?php echo e($section->settings['subtitle'] ?? ''); ?>" class="input"></div>
            <?php endif; ?>
            <div class="md:col-span-2"><button class="btn-primary">Save Section</button></div>
        </form>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2)): ?>
<?php $attributes = $__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2; ?>
<?php unset($__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2)): ?>
<?php $component = $__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2; ?>
<?php unset($__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/theme/homepage.blade.php ENDPATH**/ ?>