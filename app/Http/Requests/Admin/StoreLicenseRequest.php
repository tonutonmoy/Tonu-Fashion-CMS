<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'licensed_domain' => ['nullable', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'max:50'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'generate_key' => ['nullable', 'boolean'],
            'license_key' => ['nullable', 'string', 'max:50', 'unique:licenses,license_key'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['generate_key' => $this->boolean('generate_key', true)]);
    }
}
