@props(['name' => 'media_url', 'label' => 'Media', 'value' => ''])

<div class="media-picker" data-media-picker data-input-name="{{ $name }}" data-search-url="{{ route('admin.cms.media.search') }}">
    <label class="label">{{ $label }}</label>
    <div class="flex gap-2 mb-2">
        <input type="text" name="{{ $name }}" value="{{ old($name, $value) }}" class="input flex-1" data-media-url readonly placeholder="Select from media library">
        <button type="button" class="btn-secondary shrink-0" data-media-open>Browse</button>
        <button type="button" class="btn-secondary shrink-0 text-red-600" data-media-clear>Clear</button>
    </div>
    <div class="hidden rounded-lg border border-gray-200 overflow-hidden bg-gray-50" data-media-preview>
        <img src="" alt="" class="max-h-32 w-full object-contain">
    </div>

    <div class="fixed inset-0 z-[80] hidden" data-media-modal aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-media-close></div>
        <div class="absolute inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:w-full sm:max-w-3xl bg-white rounded-xl shadow-xl flex flex-col max-h-[90vh]">
            <div class="p-4 border-b flex items-center gap-3">
                <input type="search" class="input flex-1" placeholder="Search media..." data-media-search>
                <button type="button" class="btn-secondary" data-media-close>Close</button>
            </div>
            <div class="p-4 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 gap-3 flex-1" data-media-grid>
                <p class="col-span-full text-sm text-gray-500 text-center py-8">Type to search or open Media Library to upload files.</p>
            </div>
            <div class="p-4 border-t text-sm text-gray-500">
                <a href="{{ route('admin.cms.media.index') }}" target="_blank" class="text-brand-600 hover:underline">Open Media Library</a>
            </div>
        </div>
    </div>
</div>
