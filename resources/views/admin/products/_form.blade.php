@php $product = $product ?? null; @endphp
<div class="card p-6 space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="label">Name *</label>
            <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="input" required data-slug-source data-slug-target="#product-slug">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Slug</label>
            <input type="text" name="slug" id="product-slug" value="{{ old('slug', $product?->slug) }}" class="input" placeholder="auto-generated-from-name" data-manual="{{ old('slug', $product?->slug) ? 'true' : 'false' }}">
            <p class="text-xs text-gray-500 mt-1">URL: /products/<span id="product-slug-preview">{{ old('slug', $product?->slug ?: 'your-product') }}</span></p>
        </div>
        <div>
            <label class="label">SKU *</label>
            <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" class="input" required>
        </div>
        <div>
            <label class="label">Category *</label>
            <select name="category_id" class="input" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $product?->category_id) == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Brand</label>
            <select name="brand_id" class="input">
                <option value="">None</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(old('brand_id', $product?->brand_id) == $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Regular Price *</label>
            <input type="number" name="regular_price" step="0.01" value="{{ old('regular_price', $product?->regular_price) }}" class="input" required>
        </div>
        <div>
            <label class="label">Sale Price</label>
            <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product?->sale_price) }}" class="input">
        </div>
        <div>
            <label class="label">Stock *</label>
            <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" class="input" required>
        </div>
        <div>
            <label class="label">Status *</label>
            <select name="status" class="input" required>
                <option value="active" @selected(old('status', $product?->status?->value ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $product?->status?->value) === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="sm:col-span-2 flex flex-wrap gap-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="featured" value="1" @checked(old('featured', $product?->featured)) class="rounded">
                <span class="text-sm">Featured Product</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="free_delivery" value="1" @checked(old('free_delivery', $product?->free_delivery)) class="rounded">
                <span class="text-sm">Free Delivery</span>
            </label>
        </div>
        <div class="sm:col-span-2">
            <label class="label">Short Description</label>
            <textarea name="short_description" rows="2" class="input">{{ old('short_description', $product?->short_description) }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="label">Description</label>
            <textarea name="description" rows="5" class="input">{{ old('description', $product?->description) }}</textarea>
        </div>
        <div class="sm:col-span-2">
                <x-admin.image-uploader
                name="images"
                label="Product Images"
                :multiple="true"
                :existing="$product?->images ?? []"
                primary-name="primary_image_id"
                :primary-value="old('primary_image_id', $product?->images->firstWhere('is_primary')?->id)"
                hint="First image can be set as main · drag multiple files at once"
            />
        </div>
        <div>
            <label class="label">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $product?->meta_title) }}" class="input">
        </div>
        <div>
            <label class="label">Meta Description</label>
            <input type="text" name="meta_description" value="{{ old('meta_description', $product?->meta_description) }}" class="input">
        </div>
    </div>
</div>

<div class="card p-6" id="variant-catalog">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold">Variants (Size & Color)</h3>
            <p class="text-xs text-gray-500 mt-1">Add, edit, or remove sizes and colors below. Then pick them for each variant.</p>
        </div>
        <button type="button" id="add-variant-row" class="btn-secondary text-sm">+ Add Variant</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
        <div data-option-group="sizes">
            <p class="text-sm font-medium mb-2">Size options</p>
            <div class="flex flex-wrap gap-2 min-h-[2.5rem]" data-option-list="sizes">
                @foreach($sizes as $size)
                    <span class="variant-option-tag" data-value="{{ $size }}">
                        <span data-option-label>{{ $size }}</span>
                        <button type="button" class="variant-option-btn" data-option-edit title="Edit">✎</button>
                        <button type="button" class="variant-option-btn variant-option-btn--danger" data-option-remove title="Delete">×</button>
                        <input type="hidden" name="variant_catalog_sizes[]" value="{{ $size }}">
                    </span>
                @endforeach
            </div>
            <div class="flex gap-2 mt-3">
                <input type="text" data-option-add-input="sizes" class="input text-sm flex-1" placeholder="e.g. 3XL" maxlength="30">
                <button type="button" class="btn-secondary text-sm shrink-0" data-option-add="sizes">Add size</button>
            </div>
        </div>

        <div data-option-group="colors">
            <p class="text-sm font-medium mb-2">Color options</p>
            <div class="flex flex-wrap gap-2 min-h-[2.5rem]" data-option-list="colors">
                @foreach($colors as $color)
                    <span class="variant-option-tag" data-value="{{ $color }}">
                        <span data-option-label>{{ $color }}</span>
                        <button type="button" class="variant-option-btn" data-option-edit title="Edit">✎</button>
                        <button type="button" class="variant-option-btn variant-option-btn--danger" data-option-remove title="Delete">×</button>
                        <input type="hidden" name="variant_catalog_colors[]" value="{{ $color }}">
                    </span>
                @endforeach
            </div>
            <div class="flex gap-2 mt-3">
                <input type="text" data-option-add-input="colors" class="input text-sm flex-1" placeholder="e.g. Olive" maxlength="50">
                <button type="button" class="btn-secondary text-sm shrink-0" data-option-add="colors">Add color</button>
            </div>
        </div>
    </div>

    <div id="variants" class="space-y-4" data-sizes='@json($sizes)' data-colors='@json($colors)'>
        @php $variants = old('variants', $product?->variants?->toArray() ?? [['size' => 'M', 'color' => 'Black', 'stock' => 10]]); @endphp
        @foreach($variants as $i => $variant)
        <div class="border border-gray-200 rounded-xl p-4 space-y-3" data-variant-row>
            <div class="flex justify-between items-center">
                <p class="text-sm font-medium">Variant #{{ $i + 1 }}</p>
                <button type="button" class="text-red-600 text-sm" data-remove-variant>Remove</button>
            </div>
            @if(!empty($variant['id']))
                <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant['id'] }}">
            @endif
            <input type="hidden" name="variants[{{ $i }}][size]" data-size-input value="{{ $variant['size'] ?? '' }}">
            <input type="hidden" name="variants[{{ $i }}][color]" data-color-input value="{{ $variant['color'] ?? '' }}">
            <div>
                <p class="text-xs text-gray-500 mb-2">Size</p>
                <div class="flex flex-wrap gap-2" data-variant-size-group>
                    @foreach($sizes as $size)
                        <button type="button" class="variant-chip {{ ($variant['size'] ?? '') === $size ? 'is-active' : '' }}" data-variant-size="{{ $size }}">{{ $size }}</button>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-2">Color</p>
                <div class="flex flex-wrap gap-2" data-variant-color-group>
                    @foreach($colors as $color)
                        <button type="button" class="variant-chip {{ ($variant['color'] ?? '') === $color ? 'is-active' : '' }}" data-variant-color="{{ $color }}">{{ $color }}</button>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? 0 }}" placeholder="Stock" class="input">
                <input type="number" name="variants[{{ $i }}][price_adjustment]" value="{{ $variant['price_adjustment'] ?? 0 }}" step="0.01" placeholder="Price +/-" class="input">
            </div>
            @if(!empty($variant['image']))
                <div class="flex items-center gap-3">
                    <img src="{{ image_url($variant['image']) }}" alt="" class="w-16 h-16 rounded-lg object-cover border">
                    <label class="text-sm flex items-center gap-2"><input type="checkbox" name="variants[{{ $i }}][remove_image]" value="1"> Remove image</label>
                </div>
            @endif
                <x-admin.image-uploader name="variants[{{ $i }}][image]" label="Variant Image" :multiple="false" hint="Optional · overrides product image for this variant" />
        </div>
        @endforeach
    </div>
</div>
