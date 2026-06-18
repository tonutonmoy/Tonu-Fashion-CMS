<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'file',
    'label' => 'File',
    'accept' => 'image/*,.webp,.svg,application/pdf',
    'hint' => null,
    'required' => false,
    'buttonText' => 'Choose file',
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
    'name' => 'file',
    'label' => 'File',
    'accept' => 'image/*,.webp,.svg,application/pdf',
    'hint' => null,
    'required' => false,
    'buttonText' => 'Choose file',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="admin-uploader" data-uploader data-multiple="0" data-preview-mode="file">
    <?php if($label): ?>
        <label class="label"><?php echo e($label); ?></label>
    <?php endif; ?>

    <div class="admin-uploader-drop" data-uploader-drop tabindex="0" role="button" aria-label="Upload <?php echo e(strtolower($label)); ?>">
        <input
            type="file"
            name="<?php echo e($name); ?>"
            accept="<?php echo e($accept); ?>"
            <?php if($required): ?> required <?php endif; ?>
            class="sr-only"
            data-uploader-input
        >
        <div class="admin-uploader-drop-inner pointer-events-none">
            <div class="admin-uploader-icon admin-uploader-icon--file">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <p class="admin-uploader-title">Drag & drop your file here</p>
            <p class="admin-uploader-subtitle">Images, WebP, SVG, or PDF</p>
            <span class="admin-uploader-btn"><?php echo e($buttonText); ?></span>
            <p class="admin-uploader-status" data-uploader-status>No file selected</p>
        </div>
    </div>

    <?php if($hint): ?>
        <p class="admin-uploader-hint"><?php echo e($hint); ?></p>
    <?php endif; ?>

    <div class="admin-uploader-preview admin-uploader-preview--files mt-3 space-y-2" data-uploader-preview></div>
</div>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/admin/file-uploader.blade.php ENDPATH**/ ?>