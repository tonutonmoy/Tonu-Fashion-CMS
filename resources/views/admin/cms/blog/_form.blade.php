@php $post = $post ?? null; @endphp
<div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Title</label>
            <input name="title" class="input" required data-slug-source data-slug-target="#post-slug" value="{{ old('title', $post?->title) }}">
        </div>
        <div>
            <label class="label">Slug</label>
            <input name="slug" id="post-slug" class="input" value="{{ old('slug', $post?->slug) }}">
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Category</label>
            <select name="blog_category_id" class="input">
                <option value="">Uncategorized</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('blog_category_id', $post?->blog_category_id) == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Status</label>
            <select name="status" class="input">
                @foreach(\App\Enums\ContentStatus::cases() as $s)
                <option value="{{ $s->value }}" @selected(old('status', $post?->status?->value ?? 'draft') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="label">Excerpt</label>
        <textarea name="excerpt" class="input" rows="2">{{ old('excerpt', $post?->excerpt) }}</textarea>
    </div>
    <x-admin.rich-editor name="content" :value="old('content', $post?->content)" />

    <div class="card p-4 space-y-4 border border-gray-200 bg-gray-50/50">
        <div>
            <h3 class="font-semibold">বাংলা কন্টেন্ট (Bengali)</h3>
            <p class="text-xs text-gray-500 mt-1">Optional — shown when visitors switch to বাংলা.</p>
        </div>
        @php $bn = old('translations.bn', $post?->translations['bn'] ?? []); @endphp
        <div>
            <label class="label">Title (BN)</label>
            <input name="translations[bn][title]" class="input" value="{{ $bn['title'] ?? '' }}">
        </div>
        <div>
            <label class="label">Excerpt (BN)</label>
            <textarea name="translations[bn][excerpt]" class="input" rows="2">{{ $bn['excerpt'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="label">Content (BN)</label>
            <textarea name="translations[bn][content]" class="input" rows="6">{{ $bn['content'] ?? '' }}</textarea>
        </div>
    </div>

    <div>
        <label class="label">Tags (comma separated)</label>
        <input name="tags_input" class="input" value="{{ old('tags_input', $post?->tags?->pluck('name')->join(', ')) }}" placeholder="fashion, trends, sale" data-tags-input>
        <input type="hidden" name="tags" value="" data-tags-hidden>
    </div>
    <x-admin.image-uploader name="featured_image" label="Featured Image" :existing-url="image_url($post?->featured_image)" hint="Shown on blog list and post header · 16:9 works well" />
    <h3 class="font-semibold pt-2">SEO</h3>
    <div>
        <label class="label">Meta Title</label>
        <input name="meta_title" class="input" value="{{ old('meta_title', $post?->meta_title) }}">
    </div>
    <div>
        <label class="label">Meta Description</label>
        <textarea name="meta_description" class="input" rows="2">{{ old('meta_description', $post?->meta_description) }}</textarea>
    </div>
    <x-admin.image-uploader name="og_image" label="OG Image" :existing-url="image_url($post?->og_image)" hint="Social share preview image" />
</div>
