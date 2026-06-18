<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'cod_enabled' => ['nullable', 'boolean'],
        ];

        foreach (['bkash', 'nagad', 'sslcommerz'] as $gateway) {
            $rules["{$gateway}_enabled"] = ['nullable', 'boolean'];
            $rules["{$gateway}_sandbox"] = ['nullable', 'boolean'];
            $rules["{$gateway}_app_key"] = ['nullable', 'string', 'max:500'];
            $rules["{$gateway}_app_secret"] = ['nullable', 'string', 'max:500'];
            $rules["{$gateway}_username"] = ['nullable', 'string', 'max:255'];
            $rules["{$gateway}_password"] = ['nullable', 'string', 'max:255'];
            $rules["{$gateway}_merchant_id"] = ['nullable', 'string', 'max:255'];
            $rules["{$gateway}_store_id"] = ['nullable', 'string', 'max:255'];
            $rules["{$gateway}_base_url"] = ['nullable', 'url', 'max:500'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $merge = ['cod_enabled' => $this->boolean('cod_enabled')];

        foreach (['bkash', 'nagad', 'sslcommerz'] as $gateway) {
            $merge["{$gateway}_enabled"] = $this->boolean("{$gateway}_enabled");
            $merge["{$gateway}_sandbox"] = $this->boolean("{$gateway}_sandbox");
        }

        $this->merge($merge);
    }
}
