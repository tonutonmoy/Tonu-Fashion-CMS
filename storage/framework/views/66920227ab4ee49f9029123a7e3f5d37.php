<?php $__env->startSection('title', 'Hero Slider'); ?>
<?php $__env->startSection('content'); ?>
<?php
    $typo = hero_typography_defaults();
    $sizes = config('themes.hero_size_defaults', []);
    $copyDefaults = config('themes.hero_content_defaults', []);
    $layoutValue = old('content_layout', $hero['content_layout'] ?? 'centered');
    $showTitle = (bool) old('show_title', $hero['show_title'] ?? true);
    $showSubtitle = (bool) old('show_subtitle', $hero['show_subtitle'] ?? true);
    $showButton = (bool) old('show_button', $hero['show_button'] ?? true);
    $layoutIcons = [
        'centered' => 'justify-center items-center',
        'left' => 'justify-start items-center pl-2',
        'right' => 'justify-end items-center pr-2',
        'bottom' => 'justify-center items-end pb-1',
    ];
?>
<?php if (isset($component)) { $__componentOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25c5ac4f1fcc4585031034fd9ee8c5c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.builder-layout','data' => ['live' => true,'previewUrl' => builder_preview_url(null, 'section-hero_slider'),'previewLabel' => 'Homepage → Hero Banner','previewSection' => 'section-hero_slider']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.builder-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['live' => true,'preview-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(builder_preview_url(null, 'section-hero_slider')),'preview-label' => 'Homepage → Hero Banner','preview-section' => 'section-hero_slider']); ?>

<?php if($errors->any()): ?>
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
    <p class="font-semibold mb-1">Could not save hero slider</p>
    <ul class="list-disc list-inside space-y-0.5">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('admin.theme.hero-slides.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5" data-hero-builder data-loading-message="Saving hero slider…"
    data-max-file-mb="<?php echo e(config('uploads.hero_per_file_mb')); ?>"
    data-max-post-mb="<?php echo e(config('uploads.hero_post_max_mb')); ?>"
    data-max-files="<?php echo e(config('uploads.hero_max_files')); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    
    <div class="card p-5 space-y-4">
        <div>
            <h3 class="font-semibold text-lg">Hero Content</h3>
            <p class="text-sm text-gray-500 mt-1">Title, subtitle and button — updates live in preview.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">Title *</label>
                <input name="title" value="<?php echo e(old('title', $hero['title'] ?? '')); ?>" class="input" required placeholder="Summer Collection 2026">
            </div>
            <div>
                <label class="label">Subtitle</label>
                <input name="subtitle" value="<?php echo e(old('subtitle', $hero['subtitle'] ?? '')); ?>" class="input" placeholder="New arrivals · Free shipping">
            </div>
            <div>
                <label class="label">Button Text</label>
                <input name="button_text" value="<?php echo e(old('button_text', $hero['button_text'] ?? '')); ?>" class="input" placeholder="Shop Now">
            </div>
            <div>
                <label class="label">Button Link</label>
                <input name="button_link" value="<?php echo e(old('button_link', $hero['button_link'] ?? '')); ?>" class="input" placeholder="/shop">
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Show on banner</p>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                    <input type="hidden" name="show_title" value="0">
                    <input type="checkbox" name="show_title" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?php if($showTitle): echo 'checked'; endif; ?>>
                    Title
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                    <input type="hidden" name="show_subtitle" value="0">
                    <input type="checkbox" name="show_subtitle" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?php if($showSubtitle): echo 'checked'; endif; ?>>
                    Subtitle
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                    <input type="hidden" name="show_button" value="0">
                    <input type="checkbox" name="show_button" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?php if($showButton): echo 'checked'; endif; ?>>
                    Button
                </label>
            </div>
            <p class="text-xs text-gray-400 mt-2">Uncheck to hide an element while keeping the text saved.</p>
        </div>
    </div>

    
    <div class="card p-5 space-y-4">
        <div>
            <h3 class="font-semibold text-lg">Content Layout</h3>
            <p class="text-sm text-gray-500 mt-1">Where title and button sit on the banner.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php $__currentLoopData = $contentLayouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="group cursor-pointer rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white p-3 transition has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/40 has-[:checked]:ring-1 has-[:checked]:ring-brand-600">
                <input type="radio" name="content_layout" value="<?php echo e($key); ?>" class="sr-only" <?php if($layoutValue === $key): echo 'checked'; endif; ?>>
                <div class="aspect-[5/3] rounded-lg bg-gradient-to-br from-gray-700 to-gray-900 mb-2 relative overflow-hidden">
                    <div class="absolute inset-0 flex <?php echo e($layoutIcons[$key] ?? 'justify-center items-center'); ?>">
                        <span class="block w-8 h-1.5 rounded-full bg-white/90 shadow-sm"></span>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-800 text-center"><?php echo e($label); ?></p>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="card p-5 space-y-5">
        <div>
            <h3 class="font-semibold text-lg">Typography &amp; Button Size</h3>
            <p class="text-sm text-gray-500 mt-1">Font sizes in pixels. Button width/height empty = auto.</p>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Text sizes</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label">Title font</label>
                    <div class="relative">
                        <input type="number" name="title_size" value="<?php echo e(hero_form_typography_value($hero, 'title_size')); ?>" class="input pr-10" min="12" max="160" step="1">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">px</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Default <?php echo e($typo['title_size']); ?>px</p>
                </div>
                <div>
                    <label class="label">Subtitle font</label>
                    <div class="relative">
                        <input type="number" name="subtitle_size" value="<?php echo e(hero_form_typography_value($hero, 'subtitle_size')); ?>" class="input pr-10" min="10" max="96" step="1">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">px</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Default <?php echo e($typo['subtitle_size']); ?>px</p>
                </div>
                <div>
                    <label class="label">Button font</label>
                    <div class="relative">
                        <input type="number" name="button_size" value="<?php echo e(hero_form_typography_value($hero, 'button_size')); ?>" class="input pr-10" min="8" max="48" step="1">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">px</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Default <?php echo e($typo['button_size']); ?>px</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Button dimensions</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                <div>
                    <label class="label">Width</label>
                    <div class="relative">
                        <input type="number" name="button_width" value="<?php echo e(hero_form_size_value($hero, 'button_width')); ?>" class="input pr-10" min="0" max="600" step="1">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">px</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Default <?php echo e($sizes['button_width'] ?? 140); ?>px</p>
                </div>
                <div>
                    <label class="label">Height</label>
                    <div class="relative">
                        <input type="number" name="button_height" value="<?php echo e(hero_form_size_value($hero, 'button_height')); ?>" class="input pr-10" min="0" max="200" step="1">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">px</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Default <?php echo e($sizes['button_height'] ?? 44); ?>px</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card p-5 space-y-4">
        <div>
            <h3 class="font-semibold text-lg">Slider Settings</h3>
            <p class="text-sm text-gray-500 mt-1">Overlay and slide timing.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
            <div>
                <label class="label">Overlay color</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="overlay_color" value="<?php echo e(old('overlay_color', $hero['overlay_color'] ?? '#000000')); ?>" class="h-11 w-14 rounded-lg cursor-pointer border border-gray-200 p-1">
                    <span class="text-sm text-gray-500">Darkens the background for readable text</span>
                </div>
            </div>
            <div>
                <label class="label">Slide interval</label>
                <div class="relative">
                    <input type="number" name="autoplay_seconds" value="<?php echo e(old('autoplay_seconds', $hero['autoplay_seconds'] ?? 5)); ?>" class="input pr-14" min="3" max="30" step="1">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">sec</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Default 5 seconds</p>
            </div>
        </div>
    </div>

    
    <div class="card p-5 space-y-4">
        <div>
            <h3 class="font-semibold text-lg">Slider Media</h3>
            <p class="text-sm text-gray-500">Upload images, edit video URLs, set main slide, or remove items. Drag to reorder.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if (isset($component)) { $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.image-uploader','data' => ['name' => 'media_images','label' => 'Add images','multiple' => true,'hint' => 'PNG, JPG, WebP · max '.config('uploads.hero_per_file_mb').'MB each','buttonText' => 'Choose images']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.image-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'media_images','label' => 'Add images','multiple' => true,'hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('PNG, JPG, WebP · max '.config('uploads.hero_per_file_mb').'MB each'),'button-text' => 'Choose images']); ?>
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
            <div>
                <label class="label">Add new video URL</label>
                <input name="video_url" value="<?php echo e(old('video_url')); ?>" class="input" placeholder="youtube.com/watch?v=...">
                <p class="text-xs text-gray-400 mt-1">YouTube/Vimeo — shows as cinematic background</p>
            </div>
        </div>

        <div class="space-y-3" data-hero-media-sort>
            <?php $__empty_1 = true; $__currentLoopData = $hero['media'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-xl border border-gray-200 bg-white overflow-hidden <?php echo e($index === 0 ? 'ring-2 ring-brand-500/30' : ''); ?>" data-hero-media-id="<?php echo e($item['id']); ?>" data-hero-media-row>
                <div class="flex items-start gap-3 p-3">
                    <span class="text-gray-400 text-xl cursor-grab select-none mt-2" title="Drag to reorder">⠿</span>
                    <div class="w-28 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center border border-gray-100">
                        <?php if(($item['type'] ?? '') === 'video'): ?>
                            <?php $thumb = hero_youtube_thumbnail($item['video_url'] ?? null); ?>
                            <?php if($thumb): ?>
                                <img src="<?php echo e($thumb); ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-xs text-gray-500 px-1 text-center">Video</span>
                            <?php endif; ?>
                        <?php elseif(! empty($item['desktop_image'])): ?>
                            <img src="<?php echo e(image_url($item['desktop_image'])); ?>" alt="" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="text-xs text-gray-400">—</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold capitalize text-gray-800"><?php echo e($item['type'] ?? 'image'); ?></span>
                            <?php if($index === 0): ?>
                                <span class="text-[10px] uppercase tracking-wide font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full">Main slide</span>
                            <?php endif; ?>
                        </div>
                        <?php if(($item['type'] ?? '') === 'video'): ?>
                            <input type="url" name="media_video[<?php echo e($item['id']); ?>]" value="<?php echo e(old('media_video.'.$item['id'], $item['video_url'] ?? '')); ?>" class="input text-sm" placeholder="Video URL">
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginal0703b2d66d34e0cfa15c96063b3d553d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0703b2d66d34e0cfa15c96063b3d553d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.image-uploader','data' => ['name' => 'media_replace['.e($item['id']).']','label' => 'Replace image','compact' => true,'hint' => 'Keeps slide order · saves on Save Draft']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.image-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'media_replace['.e($item['id']).']','label' => 'Replace image','compact' => true,'hint' => 'Keeps slide order · saves on Save Draft']); ?>
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
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <?php if($index !== 0): ?>
                        <button type="button" class="text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700" data-hero-set-main="<?php echo e($item['id']); ?>">Set main</button>
                        <?php endif; ?>
                        <button type="button" class="text-xs px-2.5 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50" data-hero-remove="<?php echo e($item['id']); ?>">Delete</button>
                    </div>
                    <input type="hidden" name="remove_media[]" value="" data-hero-remove-input="<?php echo e($item['id']); ?>" disabled>
                    <input type="hidden" name="media_order[]" value="<?php echo e($item['id']); ?>" data-hero-media-order>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-gray-500 py-10 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                <p class="font-medium">No media yet</p>
                <p class="text-sm mt-1">Upload images or add a video URL above</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="btn-primary">Save Draft</button>
        <p class="text-xs text-gray-500">Preview updates as you type · Publish to go live</p>
    </div>
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
<?php if(session('refresh_preview')): ?>
<script>document.body.dataset.refreshPreview = '1';</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/theme/hero-slides.blade.php ENDPATH**/ ?>