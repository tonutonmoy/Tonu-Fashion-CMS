<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Rules\ModelExists;
use App\Rules\ModelUnique;
use App\Services\FlashSaleService;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                new ModelUnique(Product::class, 'slug', $product?->id),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                new ModelUnique(Product::class, 'sku', $product?->id),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'regular_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:regular_price'],
            'stock' => ['required', 'integer', 'min:0'],
            'featured' => ['boolean'],
            'flash_sale' => ['boolean'],
            'free_delivery' => ['boolean'],
            'category_id' => ['required', new ModelExists(Category::class)],
            'brand_id' => ['nullable', new ModelExists(Brand::class)],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'primary_image_id' => ['nullable', 'integer', new ModelExists(ProductImage::class)],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', new ModelExists(ProductVariant::class)],
            'variants.*.size' => ['nullable', 'string', 'max:30'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.image' => ['nullable', 'image', 'max:4096'],
            'variants.*.remove_image' => ['nullable', 'boolean'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', new ModelExists(ProductImage::class)],
            'variant_catalog_sizes' => ['nullable', 'array'],
            'variant_catalog_sizes.*' => ['string', 'max:30'],
            'variant_catalog_colors' => ['nullable', 'array'],
            'variant_catalog_colors.*' => ['string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $flashSectionOn = app(FlashSaleService::class)->isSectionEnabledInBuilder();

        $this->merge([
            'featured' => $this->boolean('featured'),
            'flash_sale' => $flashSectionOn ? $this->boolean('flash_sale') : false,
            'free_delivery' => $this->boolean('free_delivery'),
        ]);
    }
}
