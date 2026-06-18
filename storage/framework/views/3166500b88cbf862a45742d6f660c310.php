<?php $__env->startSection('title', 'Categories'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex justify-between mb-6">
    <h2 class="text-xl font-semibold">Categories</h2>
    <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn-primary">Add Category</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Products</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
        <tbody class="divide-y">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="px-4 py-3 font-medium"><?php echo e($category->name); ?></td>
                <td class="px-4 py-3"><?php echo e($category->products_count); ?></td>
                <td class="px-4 py-3"><?php echo e($category->status->label()); ?></td>
                <td class="px-4 py-3 text-right">
                    <?php if (isset($component)) { $__componentOriginal7c8dfb2426b346a5c74079e742032e01 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c8dfb2426b346a5c74079e742032e01 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-group','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'edit','href' => route('admin.categories.edit', $category)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'edit','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.categories.edit', $category))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $attributes = $__attributesOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__attributesOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $component = $__componentOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__componentOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'delete','action' => route('admin.categories.destroy', $category),'method' => 'DELETE','confirm' => true,'confirmMessage' => __('admin.confirm_delete').' '.$category->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'delete','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.categories.destroy', $category)),'method' => 'DELETE','confirm' => true,'confirm-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.confirm_delete').' '.$category->name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $attributes = $__attributesOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__attributesOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $component = $__componentOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__componentOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c8dfb2426b346a5c74079e742032e01)): ?>
<?php $attributes = $__attributesOriginal7c8dfb2426b346a5c74079e742032e01; ?>
<?php unset($__attributesOriginal7c8dfb2426b346a5c74079e742032e01); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c8dfb2426b346a5c74079e742032e01)): ?>
<?php $component = $__componentOriginal7c8dfb2426b346a5c74079e742032e01; ?>
<?php unset($__componentOriginal7c8dfb2426b346a5c74079e742032e01); ?>
<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php echo e($categories->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>