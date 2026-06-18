<?php

namespace App\Http\Requests\Admin;

use App\Enums\LicenseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLicenseRequest extends FormRequest
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
            'status' => ['nullable', Rule::enum(LicenseStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
