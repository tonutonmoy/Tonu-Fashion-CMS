<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Rules\MongoExists;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', new MongoExists(Product::class)],
            'product_variant_id' => ['nullable', new MongoExists(ProductVariant::class)],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }
}
