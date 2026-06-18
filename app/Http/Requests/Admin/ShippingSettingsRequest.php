<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'inside_dhaka' => ['required', 'numeric', 'min:0'],
            'outside_dhaka' => ['required', 'numeric', 'min:0'],
            'free_shipping_limit' => ['required', 'numeric', 'min:0'],
        ];
    }
}
