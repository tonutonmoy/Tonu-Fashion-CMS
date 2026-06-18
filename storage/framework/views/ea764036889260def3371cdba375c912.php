<?php if(isset($sections['blog']) && $sections['blog']['posts']->isNotEmpty()): ?>
<section class="theme-section">
    <div class="theme-container">
        <div class="theme-section-header">
            <h2 class="theme-section-title">From Our Blog</h2>
            <a href="<?php echo e(route('blog.index')); ?>" class="theme-link">View all</a>
        </div>
        <div class="theme-blog-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $sections['blog']['posts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="theme-blog-card card overflow-hidden">
                <?php if($post->featured_image): ?>
                    <a href="<?php echo e(route('blog.show', $post->slug)); ?>">
                        <img src="<?php echo e(image_url($post->featured_image)); ?>" alt="<?php echo e(trans_field($post, 'title')); ?>" loading="lazy" class="w-full h-48 object-cover">
                    </a>
                <?php endif; ?>
                <div class="p-4">
                    <h3 class="font-semibold"><a href="<?php echo e(route('blog.show', $post->slug)); ?>" class="hover:text-brand-600"><?php echo e(trans_field($post, 'title')); ?></a></h3>
                    <p class="text-sm text-gray-500 mt-2 line-clamp-3"><?php echo e(trans_field($post, 'excerpt')); ?></p>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/blog.blade.php ENDPATH**/ ?>