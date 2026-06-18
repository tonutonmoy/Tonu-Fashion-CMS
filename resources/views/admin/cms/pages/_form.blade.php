@php $page = $page ?? null; @endphp
<div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Title</label>
            <input name="title" class="input" required data-slug-source data-slug-target="#page-slug" value="{{ old('title', $page?->title) }}">
        </div>
        <div>
            <label class="label">Slug</label>
            <input name="slug" id="page-slug" class="input" value="{{ old('slug', $page?->slug) }}" placeholder="auto-generated">
        </div>
    </div>
    <div>
        <label class="label">Status</label>
        <select name="status" class="input">
            @foreach(\App\Enums\ContentStatus::cases() as $s)
            <option value="{{ $s->value }}" @selected(old('status', $page?->status?->value ?? 'draft') === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
    </div>
    <x-admin.rich-editor name="content" :value="old('content', $page?->content)" />
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-admin.image-uploader name="banner_image" label="Page Banner" :existing-url="image_url($page?->banner_image)" hint="Wide banner at top of page · 21:9 recommended" />
        <x-admin.image-uploader name="og_image" label="OG Image" :existing-url="image_url($page?->og_image)" hint="Social share preview image" />
    </div>
    <h3 class="font-semibold pt-2">SEO</h3>
    <div>
        <label class="label">Meta Title</label>
        <input name="meta_title" class="input" value="{{ old('meta_title', $page?->meta_title) }}">
    </div>
    <div>
        <label class="label">Meta Description</label>
        <textarea name="meta_description" class="input" rows="2">{{ old('meta_description', $page?->meta_description) }}</textarea>
    </div>
</div>
