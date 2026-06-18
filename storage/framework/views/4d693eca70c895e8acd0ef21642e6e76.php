<?php $__env->startSection('content'); ?>
<div class="theme-container py-8">
    <h1 class="theme-page-title mb-8">Blog</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="card overflow-hidden">
            <?php if($post->featured_image): ?>
                <a href="<?php echo e(route('blog.show', $post->slug)); ?>"><img src="<?php echo e(image_url($post->featured_image)); ?>" alt="" class="w-full h-48 object-cover" loading="lazy"></a>
            <?php endif; ?>
            <div class="p-4">
                <h2 class="font-semibold text-lg"><a href="<?php echo e(route('blog.show', $post->slug)); ?>"><?php echo e(trans_field($post, 'title')); ?></a></h2>
                <p class="text-sm text-gray-500 mt-2"><?php echo e(trans_field($post, 'excerpt')); ?></p>
            </div>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-8"><?php echo e($posts->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(theme_layout(), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/blog/index.blade.php ENDPATH**/ ?>