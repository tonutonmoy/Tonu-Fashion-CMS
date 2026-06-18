<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'image',
    'label' => 'Image',
    'multiple' => false,
    'accept' => 'image/*',
    'existing' => null,
    'existingUrl' => null,
    'primaryName' => null,
    'primaryValue' => null,
    'hint' => null,
    'compact' => false,
    'required' => false,
    'buttonText' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name' => 'image',
    'label' => 'Image',
    'multiple' => false,
    'accept' => 'image/*',
    'existing' => null,
    'existingUrl' => null,
    'primaryName' => null,
    'primaryValue' => null,
    'hint' => null,
    'compact' => false,
    'required' => false,
    'buttonText' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $existingItems = collect($existing ?? []);
    if ($existingUrl && $existingItems->isEmpty()) {
        $existingItems = collect([['url' => $existingUrl, 'id' => null]]);
    }
    $inputName = $multiple ? $name.'[]' : $name;
    $browseLabel = $buttonText ?? ($multiple ? 'Choose images' : 'Choose file');
?>

<div
    class="admin-uploader <?php echo e($compact ? 'admin-uploader--compact' : ''); ?>"
    data-uploader
    data-multiple="<?php echo e($multiple ? '1' : '0'); ?>"
    data-preview-mode="image"
>
    <?php if($label): ?>
        <label class="label"><?php echo e($label); ?></label>
    <?php endif; ?>

    <?php if($existingItems->isNotEmpty()): ?>
        <div class="admin-uploader-existing grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
            <?php $__currentLoopData = $existingItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $itemId = is_object($item) ? $item->id : ($item['id'] ?? null);
                    $itemUrl = is_object($item) ? image_url($item->path ?? $item->logo ?? $item->image ?? '') : ($item['url'] ?? '');
                    $isPrimary = is_object($item) ? ($item->is_primary ?? false) : ($item['is_primary'] ?? false);
                ?>
                <div class="admin-uploader-item relative group rounded-xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm" data-existing-id="<?php echo e($itemId); ?>">
                    <img src="<?php echo e($itemUrl); ?>" alt="" class="w-full h-32 object-cover">
                    <?php if($primaryName && $itemId): ?>
                        <label class="absolute top-2 left-2 bg-white/95 rounded-full px-2.5 py-1 text-xs font-medium flex items-center gap-1.5 cursor-pointer shadow-sm">
                            <input type="radio" name="<?php echo e($primaryName); ?>" value="<?php echo e($itemId); ?>" <?php if((string) old($primaryName, $primaryValue) === (string) $itemId || $isPrimary): echo 'checked'; endif; ?>>
                            Main
                        </label>
                    <?php endif; ?>
                    <?php if($itemId): ?>
                        <input type="hidden" name="remove_images[]" value="" disabled data-remove-input>
                        <button type="button" class="admin-uploader-remove" data-remove-existing="<?php echo e($itemId); ?>" title="Remove" aria-label="Remove image">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="admin-uploader-drop" data-uploader-drop tabindex="0" role="button" aria-label="Upload <?php echo e(strtolower($label)); ?>">
        <input
            type="file"
            name="<?php echo e($inputName); ?>"
            accept="<?php echo e($accept); ?>"
            <?php if($multiple): ?> multiple <?php endif; ?>
            <?php if($required): ?> required <?php endif; ?>
            class="sr-only"
            data-uploader-input
        >
        <div class="admin-uploader-drop-inner pointer-events-none">
            <div class="admin-uploader-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="admin-uploader-title">
                <?php if($compact): ?>
                    Drop image or <span class="text-brand-600">browse</span>
                <?php else: ?>
                    Drag & drop <?php echo e($multiple ? 'images' : 'an image'); ?> here
                <?php endif; ?>
            </p>
            <?php if (! ($compact)): ?>
            <p class="admin-uploader-subtitle">or click to choose from your device</p>
            <span class="admin-uploader-btn"><?php echo e($browseLabel); ?></span>
            <?php endif; ?>
            <p class="admin-uploader-status" data-uploader-status>No file selected</p>
        </div>
    </div>

    <?php if($hint): ?>
        <p class="admin-uploader-hint"><?php echo e($hint); ?></p>
    <?php endif; ?>

    <div class="admin-uploader-preview grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3" data-uploader-preview></div>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/admin/image-uploader.blade.php ENDPATH**/ ?>