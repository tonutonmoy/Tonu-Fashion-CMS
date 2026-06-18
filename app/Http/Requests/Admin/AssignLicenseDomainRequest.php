<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignLicenseDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'licensed_domain' => ['required', 'string', 'max:255'],
        ];
    }
}
