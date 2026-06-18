<?php
    $heroConfig = $sections['hero_slider']['config'] ?? [];
    $media = $heroConfig['media'] ?? [];
    $heroLayouts = config('themes.hero_content_layouts', []);
    $layout = $heroConfig['content_layout'] ?? 'centered';
    if (! array_key_exists($layout, $heroLayouts)) {
        $layout = 'centered';
    }
    $contentStyleVars = hero_slide_style_vars((object) $heroConfig);
    $autoplayMs = max(3000, (int) (($heroConfig['autoplay_seconds'] ?? 5) * 1000));
    $showTitle = (bool) ($heroConfig['show_title'] ?? true);
    $showSubtitle = (bool) ($heroConfig['show_subtitle'] ?? true);
    $showButton = (bool) ($heroConfig['show_button'] ?? true);
?>
<?php if(! empty($media)): ?>
<section class="theme-section theme-hero p-0" id="section-hero_slider">
    <div
        class="theme-hero-slider"
        data-hero-slider
        data-slides="<?php echo e(count($media)); ?>"
        data-autoplay="<?php echo e($autoplayMs); ?>"
    >
        <div class="theme-hero-media-track">
            <?php $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('themes.shared.sections._hero_slide', ['item' => $item, 'heroConfig' => $heroConfig, 'isFirst' => $loop->first], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="theme-hero-overlay" data-hero-live-overlay style="--hero-overlay-tint: <?php echo e(hero_overlay_rgba($heroConfig['overlay_color'] ?? null)); ?>;"></div>

        <div
            class="theme-hero-content theme-hero-content--<?php echo e($layout); ?>"
            style="<?php echo e($contentStyleVars); ?>"
            data-hero-layout="<?php echo e($layout); ?>"
            data-hero-live-content
        >
            <div class="theme-hero-content-inner">
                <h1 class="theme-hero-title" data-hero-live-title <?php if(!$showTitle || empty($heroConfig['title'])): ?> style="display:none" <?php endif; ?>><?php echo e($heroConfig['title'] ?? ''); ?></h1>
                <p class="theme-hero-subtitle" data-hero-live-subtitle <?php if(!$showSubtitle || empty($heroConfig['subtitle'])): ?> style="display:none" <?php endif; ?>><?php echo e($heroConfig['subtitle'] ?? ''); ?></p>
                <a
                    href="<?php echo e($heroConfig['button_link'] ?? '#'); ?>"
                    class="theme-btn theme-btn-primary theme-hero-btn"
                    data-hero-live-button
                    <?php if(!$showButton || empty($heroConfig['button_text']) || empty($heroConfig['button_link'])): ?> style="display:none" <?php endif; ?>
                ><?php echo e($heroConfig['button_text'] ?? ''); ?></a>
            </div>
        </div>

        <?php if(count($media) > 1): ?>
        <button type="button" class="theme-hero-arrow theme-hero-prev" data-hero-prev aria-label="Previous slide">‹</button>
        <button type="button" class="theme-hero-arrow theme-hero-next" data-hero-next aria-label="Next slide">›</button>
        <div class="theme-hero-dots" data-hero-dots>
            <?php $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" class="theme-hero-dot <?php echo e($loop->first ? 'is-active' : ''); ?>" data-hero-dot="<?php echo e($loop->index); ?>" aria-label="Go to slide <?php echo e($loop->iteration); ?>"></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/hero_slider.blade.php ENDPATH**/ ?>