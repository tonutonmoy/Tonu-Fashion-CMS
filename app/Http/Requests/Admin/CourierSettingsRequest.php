<?php

namespace App\Http\Requests\Admin;

use App\Enums\CourierType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourierSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        $couriers = array_column(CourierType::cases(), 'value');

        $rules = [
            'default_courier' => ['required', Rule::in($couriers)],
            'auto_parcel_enabled' => ['nullable', 'boolean'],
        ];

        foreach (CourierType::cases() as $courier) {
            $key = $courier->value;
            $rules["{$key}_enabled"] = ['nullable', 'boolean'];
            $rules["{$key}_api_key"] = ['nullable', 'string', 'max:500'];
            $rules["{$key}_secret_key"] = ['nullable', 'string', 'max:500'];
            $rules["{$key}_base_url"] = ['nullable', 'url', 'max:500'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $merge = ['auto_parcel_enabled' => $this->boolean('auto_parcel_enabled')];

        foreach (['steadfast', 'pathao', 'redx'] as $key) {
            $merge["{$key}_enabled"] = $this->boolean("{$key}_enabled");
        }

        $this->merge($merge);
    }
}
