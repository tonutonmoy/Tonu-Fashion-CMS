<?php $__env->startSection('title', 'Footer Builder'); ?>
<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.builder-layout','data' => ['previewUrl' => builder_preview_url(null, 'site-footer'),'previewLabel' => 'Homepage → Footer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.builder-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preview-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(builder_preview_url(null, 'site-footer')),'preview-label' => 'Homepage → Footer']); ?>
<form action="<?php echo e(route('admin.theme.footer')); ?>" method="POST" enctype="multipart/form-data" class="max-w-2xl card p-6 space-y-4">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <?php if (isset($component)) { $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.image-uploader','data' => ['name' => 'logo','label' => 'Footer Logo','existingUrl' => image_url($settings->logo),'hint' => 'Optional logo in footer area']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.image-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'logo','label' => 'Footer Logo','existing-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(image_url($settings->logo)),'hint' => 'Optional logo in footer area']); ?>
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
    <div><label class="label">Description</label><textarea name="description" class="input" rows="3"><?php echo e($settings->description); ?></textarea></div>
    <div><label class="label">Address</label><textarea name="address" class="input" rows="2"><?php echo e($settings->address); ?></textarea></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="label">Phone</label><input name="phone" value="<?php echo e($settings->phone); ?>" class="input"></div>
        <div><label class="label">Email</label><input name="email" type="email" value="<?php echo e($settings->email); ?>" class="input"></div>
    </div>
    <div><label class="label">Facebook URL</label><input name="facebook_url" value="<?php echo e($settings->facebook_url); ?>" class="input"></div>
    <div><label class="label">Instagram URL</label><input name="instagram_url" value="<?php echo e($settings->instagram_url); ?>" class="input"></div>
    <div><label class="label">YouTube URL</label><input name="youtube_url" value="<?php echo e($settings->youtube_url); ?>" class="input"></div>
    <div><label class="label">WhatsApp Number</label><input name="whatsapp_number" value="<?php echo e($settings->whatsapp_number); ?>" class="input"></div>
    <div><label class="label">Messenger Link</label><input name="messenger_link" value="<?php echo e($settings->messenger_link); ?>" class="input"></div>
    <div><label class="label">Copyright Text</label><input name="copyright_text" value="<?php echo e($settings->copyright_text); ?>" class="input"></div>
    <button class="btn-primary">Save Draft</button>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/theme/footer.blade.php ENDPATH**/ ?>