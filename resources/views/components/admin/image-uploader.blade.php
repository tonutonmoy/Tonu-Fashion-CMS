@props([
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
    'paletteOnly' => false,
])

@php
    $existingItems = collect($existing ?? []);
    if ($existingUrl && $existingItems->isEmpty()) {
        $existingItems = collect([['url' => $existingUrl, 'id' => null]]);
    }
    $inputName = $multiple ? $name.'[]' : $name;
    $browseLabel = $buttonText ?? ($multiple ? 'Choose images' : 'Choose file');
@endphp

<div
    class="admin-uploader {{ $compact ? 'admin-uploader--compact' : '' }}"
    data-uploader
    data-multiple="{{ $multiple ? '1' : '0' }}"
    data-preview-mode="image"
>
    @if($label)
        <label class="label">{{ $label }}</label>
    @endif

    @if($existingItems->isNotEmpty())
        <div class="admin-uploader-existing grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
            @foreach($existingItems as $item)
                @php
                    $itemId = is_object($item) ? $item->id : ($item['id'] ?? null);
                    $itemUrl = is_object($item) ? image_url($item->path ?? $item->logo ?? $item->image ?? '') : ($item['url'] ?? '');
                    $isPrimary = is_object($item) ? ($item->is_primary ?? false) : ($item['is_primary'] ?? false);
                @endphp
                <div class="admin-uploader-item relative group rounded-xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm" data-existing-id="{{ $itemId }}">
                    <img src="{{ $itemUrl }}" alt="" class="w-full h-32 object-cover">
                    @if($primaryName && $itemId)
                        <label class="absolute top-2 left-2 bg-white/95 rounded-full px-2.5 py-1 text-xs font-medium flex items-center gap-1.5 cursor-pointer shadow-sm">
                            <input type="radio" name="{{ $primaryName }}" value="{{ $itemId }}" @checked((string) old($primaryName, $primaryValue) === (string) $itemId || $isPrimary)>
                            Main
                        </label>
                    @endif
                    @if($itemId)
                        <input type="hidden" name="remove_images[]" value="" disabled data-remove-input>
                        <button type="button" class="admin-uploader-remove" data-remove-existing="{{ $itemId }}" title="Remove" aria-label="Remove image">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="admin-uploader-drop" data-uploader-drop tabindex="0" role="button" aria-label="Upload {{ strtolower($label ?: 'image') }}">
        <input
            type="file"
            @unless($paletteOnly) name="{{ $inputName }}" @endunless
            accept="{{ $accept }}"
            @if($multiple) multiple @endif
            @if($required) required @endif
            class="sr-only"
            data-uploader-input
            @if($paletteOnly) data-theme-palette-input @endif
        >
        <div class="admin-uploader-drop-inner pointer-events-none">
            <div class="admin-uploader-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="admin-uploader-title">
                @if($compact)
                    Drop image or <span class="text-brand-600">browse</span>
                @else
                    Drag & drop {{ $multiple ? 'images' : 'an image' }} here
                @endif
            </p>
            @unless($compact)
            <p class="admin-uploader-subtitle">or click to choose from your device</p>
            <span class="admin-uploader-btn">{{ $browseLabel }}</span>
            @endunless
            <p class="admin-uploader-status" data-uploader-status>No file selected</p>
        </div>
    </div>

    @if($hint)
        <p class="admin-uploader-hint">{{ $hint }}</p>
    @endif

    <div class="admin-uploader-preview grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3" data-uploader-preview></div>
</div>
