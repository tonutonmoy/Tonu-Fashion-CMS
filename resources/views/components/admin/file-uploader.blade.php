@props([
    'name' => 'file',
    'label' => 'File',
    'accept' => 'image/*,.webp,.svg,application/pdf',
    'hint' => null,
    'required' => false,
    'buttonText' => 'Choose file',
])

<div class="admin-uploader" data-uploader data-multiple="0" data-preview-mode="file">
    @if($label)
        <label class="label">{{ $label }}</label>
    @endif

    <div class="admin-uploader-drop" data-uploader-drop tabindex="0" role="button" aria-label="Upload {{ strtolower($label) }}">
        <input
            type="file"
            name="{{ $name }}"
            accept="{{ $accept }}"
            @if($required) required @endif
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
            <span class="admin-uploader-btn">{{ $buttonText }}</span>
            <p class="admin-uploader-status" data-uploader-status>No file selected</p>
        </div>
    </div>

    @if($hint)
        <p class="admin-uploader-hint">{{ $hint }}</p>
    @endif

    <div class="admin-uploader-preview admin-uploader-preview--files mt-3 space-y-2" data-uploader-preview></div>
</div>
