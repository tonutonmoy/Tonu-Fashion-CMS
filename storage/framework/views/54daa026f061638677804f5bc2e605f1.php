<?php $__env->startSection('title', 'Media Library'); ?>
<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.builder-layout','data' => ['previewUrl' => builder_preview_url(),'previewLabel' => 'Homepage']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.builder-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(builder_preview_url()),'preview-label' => 'Homepage']); ?>
<div class="flex flex-col gap-6 mb-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h2 class="text-xl font-semibold">Media Library</h2>
            <p class="text-sm text-gray-500 mt-1">Upload once, reuse across products, pages, and blog.</p>
        </div>
    </div>

    <form action="<?php echo e(route('admin.cms.media.store')); ?>" method="POST" enctype="multipart/form-data" class="card p-5">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="label">Folder</label>
                <select name="folder" class="input">
                    <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($folder); ?>" <?php if(request('folder') === $folder): echo 'selected'; endif; ?>><?php echo e($folder); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <?php if (isset($component)) { $__componentOriginal0138529aba7ba6ed4d54ccec51569dfb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0138529aba7ba6ed4d54ccec51569dfb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.file-uploader','data' => ['name' => 'file','label' => 'Upload file','accept' => 'image/*,.webp,.svg,application/pdf','required' => true,'hint' => 'Max 16MB · images, WebP, SVG, or PDF','buttonText' => 'Select file']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.file-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file','label' => 'Upload file','accept' => 'image/*,.webp,.svg,application/pdf','required' => true,'hint' => 'Max 16MB · images, WebP, SVG, or PDF','button-text' => 'Select file']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0138529aba7ba6ed4d54ccec51569dfb)): ?>
<?php $attributes = $__attributesOriginal0138529aba7ba6ed4d54ccec51569dfb; ?>
<?php unset($__attributesOriginal0138529aba7ba6ed4d54ccec51569dfb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0138529aba7ba6ed4d54ccec51569dfb)): ?>
<?php $component = $__componentOriginal0138529aba7ba6ed4d54ccec51569dfb; ?>
<?php unset($__componentOriginal0138529aba7ba6ed4d54ccec51569dfb); ?>
<?php endif; ?>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button class="btn-primary">Upload to library</button>
        </div>
    </form>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input name="q" value="<?php echo e(request('q')); ?>" class="input max-w-xs" placeholder="Search files...">
    <select name="folder" class="input max-w-[10rem]">
        <option value="">All folders</option>
        <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($folder); ?>" <?php if(request('folder') === $folder): echo 'selected'; endif; ?>><?php echo e($folder); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="btn-secondary">Filter</button>
</form>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card p-2 group relative overflow-hidden">
        <?php if(str_starts_with($item->mime_type, 'image/')): ?>
        <img src="<?php echo e($item->url); ?>" alt="<?php echo e($item->alt ?? $item->filename); ?>" class="w-full h-28 object-cover rounded-lg" loading="lazy">
        <?php else: ?>
        <div class="w-full h-28 flex items-center justify-center bg-gray-100 rounded-lg text-xs font-semibold text-gray-500 uppercase"><?php echo e(pathinfo($item->filename, PATHINFO_EXTENSION)); ?></div>
        <?php endif; ?>
        <p class="text-xs text-gray-600 mt-2 truncate font-medium" title="<?php echo e($item->filename); ?>"><?php echo e($item->filename); ?></p>
        <p class="text-xs text-gray-400"><?php echo e($item->folder); ?></p>
        <div class="mt-2 flex gap-2 opacity-0 group-hover:opacity-100 transition">
            <button type="button" class="text-xs text-brand-600 font-medium" data-copy-url="<?php echo e($item->url); ?>">Copy URL</button>
            <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'delete','action' => route('admin.cms.media.destroy', $item),'method' => 'DELETE','confirm' => true,'confirmMessage' => __('admin.confirm_delete').' '.$item->filename]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'delete','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.cms.media.destroy', $item)),'method' => 'DELETE','confirm' => true,'confirm-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.confirm_delete').' '.$item->filename)]); ?>
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
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-full">
        <div class="admin-uploader-drop pointer-events-none opacity-80">
            <div class="admin-uploader-drop-inner">
                <div class="admin-uploader-icon admin-uploader-icon--file mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <p class="admin-uploader-title">No media files yet</p>
                <p class="admin-uploader-subtitle">Upload images, WebP, SVG, or PDF using the form above</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<div class="mt-6"><?php echo e($media->links()); ?></div>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/cms/media/index.blade.php ENDPATH**/ ?>