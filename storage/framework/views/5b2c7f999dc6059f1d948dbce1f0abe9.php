<?php $__env->startSection('title', 'Blog Posts'); ?>
<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.builder-layout','data' => ['previewUrl' => builder_preview_url('/blog'),'previewLabel' => 'Blog listing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.builder-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(builder_preview_url('/blog')),'preview-label' => 'Blog listing']); ?>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold">Blog</h2>
    <a href="<?php echo e(route('admin.cms.blog.create')); ?>" class="btn-primary">New Post</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 font-medium"><?php echo e($post->title); ?></td>
                <td class="px-4 py-3 text-gray-500"><?php echo e($post->category?->name ?? '—'); ?></td>
                <td class="px-4 py-3">
                    <span class="badge <?php echo e($post->status->value === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'); ?>"><?php echo e($post->status->label()); ?></span>
                </td>
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
                        <?php if($post->status->value === 'published'): ?>
                        <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'external','href' => route('blog.show', $post->slug),'target' => '_blank']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'external','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('blog.show', $post->slug)),'target' => '_blank']); ?>
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
                        <?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'edit','href' => route('admin.cms.blog.edit', $post)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'edit','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.cms.blog.edit', $post))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'delete','action' => route('admin.cms.blog.destroy', $post),'method' => 'DELETE','confirm' => true,'confirmMessage' => __('admin.confirm_delete').' '.$post->title]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'delete','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.cms.blog.destroy', $post)),'method' => 'DELETE','confirm' => true,'confirm-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.confirm_delete').' '.$post->title)]); ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No posts yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($posts->links()); ?></div>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/cms/blog/index.blade.php ENDPATH**/ ?>