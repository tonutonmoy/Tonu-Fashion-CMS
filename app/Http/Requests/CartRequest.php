<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Rules\ModelExists;
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
            'product_id' => ['required', new ModelExists(Product::class)],
            'product_variant_id' => ['nullable', new ModelExists(ProductVariant::class)],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }
}
