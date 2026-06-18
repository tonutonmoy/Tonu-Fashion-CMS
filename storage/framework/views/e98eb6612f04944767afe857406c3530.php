<?php $__env->startSection('title', 'Theme SEO'); ?>
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
<form action="<?php echo e(route('admin.theme.seo')); ?>" method="POST" enctype="multipart/form-data" class="max-w-2xl card p-6 space-y-4">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div><label class="label">Meta Title</label><input name="meta_title" value="<?php echo e($settings->meta_title); ?>" class="input"></div>
    <div><label class="label">Meta Description</label><textarea name="meta_description" class="input" rows="3"><?php echo e($settings->meta_description); ?></textarea></div>
    <?php if (isset($component)) { $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.image-uploader','data' => ['name' => 'og_image','label' => 'Open Graph Image','existingUrl' => image_url($settings->og_image),'hint' => '1200×630px recommended · used when sharing on Facebook, WhatsApp, etc.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.image-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'og_image','label' => 'Open Graph Image','existing-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(image_url($settings->og_image)),'hint' => '1200×630px recommended · used when sharing on Facebook, WhatsApp, etc.']); ?>
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
    <div><label class="label">JSON-LD Schema (optional custom JSON)</label>
        <textarea name="json_ld_schema" class="input font-mono text-xs" rows="8" placeholder='Leave empty for auto-generated schema'><?php echo e($settings->json_ld_schema ? json_encode($settings->json_ld_schema, JSON_PRETTY_PRINT) : ''); ?></textarea>
    </div>
    <button class="btn-primary">Save SEO Settings</button>
</form>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/theme/seo.blade.php ENDPATH**/ ?>