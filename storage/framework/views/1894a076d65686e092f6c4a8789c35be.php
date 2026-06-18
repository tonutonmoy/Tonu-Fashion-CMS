<?php
    $embedUrl = ($item['type'] ?? '') === 'video' ? hero_video_embed_url($item['video_url'] ?? null) : null;
    $isVideoFile = hero_video_is_file($item['video_url'] ?? null);
    $ytThumb = hero_youtube_thumbnail($item['video_url'] ?? null);
?>
<div class="theme-hero-slide <?php echo e(($isFirst ?? false) ? 'is-active' : ''); ?>" data-hero-slide>
    <?php if($embedUrl && ! $isVideoFile && $ytThumb): ?>
        <img src="<?php echo e($ytThumb); ?>" alt="<?php echo e($heroConfig['title'] ?? 'Hero'); ?>" loading="<?php echo e(($isFirst ?? false) ? 'eager' : 'lazy'); ?>" decoding="async" class="theme-hero-bg theme-hero-video-poster">
    <?php elseif($embedUrl): ?>
        <div class="theme-hero-bg theme-hero-video">
            <?php if($isVideoFile): ?>
                <video class="theme-hero-video-file" src="<?php echo e($embedUrl); ?>" autoplay muted loop playsinline></video>
            <?php else: ?>
                <iframe src="<?php echo e($embedUrl); ?>" title="<?php echo e($heroConfig['title'] ?? 'Hero'); ?>" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen tabindex="-1"></iframe>
            <?php endif; ?>
        </div>
    <?php elseif(! empty($item['desktop_image'])): ?>
        <picture>
            <?php if(! empty($item['mobile_image'])): ?>
                <source media="(max-width: 768px)" srcset="<?php echo e(image_url($item['mobile_image'])); ?>">
            <?php endif; ?>
            <img src="<?php echo e(image_url($item['desktop_image'])); ?>" alt="<?php echo e($heroConfig['title'] ?? 'Hero'); ?>" loading="<?php echo e($loop->first ? 'eager' : 'lazy'); ?>" decoding="async" class="theme-hero-bg">
        </picture>
    <?php else: ?>
        <div class="theme-hero-bg theme-hero-placeholder"></div>
    <?php endif; ?>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/sections/_hero_slide.blade.php ENDPATH**/ ?>