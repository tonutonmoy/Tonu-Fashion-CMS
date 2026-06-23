<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->canAdmin('store');
    }

    public function rules(): array
    {
        return [
            'variant_id' => ['nullable', 'string'],
            'product_id' => ['nullable', 'string', 'required_without:variant_id'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'note' => ['required', 'string', 'max:500'],
        ];
    }
}
