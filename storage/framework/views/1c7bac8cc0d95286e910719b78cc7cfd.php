<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold">Products</h2>
    <a href="<?php echo e(route('admin.products.create')); ?>" class="btn-primary">Add Product</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">SKU</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-right">Price</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="px-4 py-3 font-medium"><?php echo e($product->name); ?></td>
                <td class="px-4 py-3 text-gray-500"><?php echo e($product->sku); ?></td>
                <td class="px-4 py-3"><?php echo e($product->category?->name); ?></td>
                <td class="px-4 py-3 text-right"><?php echo e(format_bdt($product->effective_price)); ?></td>
                <td class="px-4 py-3"><span class="badge <?php echo e($product->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e($product->status->label()); ?></span></td>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'edit','href' => route('admin.products.edit', $product)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'edit','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.products.edit', $product))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'delete','action' => route('admin.products.destroy', $product),'method' => 'DELETE','confirm' => true,'confirmMessage' => __('admin.confirm_delete').' '.$product->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'delete','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.products.destroy', $product)),'method' => 'DELETE','confirm' => true,'confirm-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.confirm_delete').' '.$product->name)]); ?>
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
<div class="mt-4"><?php echo e($products->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/products/index.blade.php ENDPATH**/ ?>