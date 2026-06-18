<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Rules\MongoExists;
use App\Rules\MongoUnique;
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
                new MongoUnique(Product::class, 'slug', $product?->id),
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                new MongoUnique(Product::class, 'sku', $product?->id),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'regular_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:regular_price'],
            'stock' => ['required', 'integer', 'min:0'],
            'featured' => ['boolean'],
            'free_delivery' => ['boolean'],
            'category_id' => ['required', new MongoExists(Category::class)],
            'brand_id' => ['nullable', new MongoExists(Brand::class)],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'primary_image_id' => ['nullable', 'integer', new MongoExists(ProductImage::class)],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', new MongoExists(ProductVariant::class)],
            'variants.*.size' => ['nullable', 'string', 'max:30'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.image' => ['nullable', 'image', 'max:4096'],
            'variants.*.remove_image' => ['nullable', 'boolean'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', new MongoExists(ProductImage::class)],
            'variant_catalog_sizes' => ['nullable', 'array'],
            'variant_catalog_sizes.*' => ['string', 'max:30'],
            'variant_catalog_colors' => ['nullable', 'array'],
            'variant_catalog_colors.*' => ['string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'free_delivery' => $this->boolean('free_delivery'),
        ]);
    }
}
