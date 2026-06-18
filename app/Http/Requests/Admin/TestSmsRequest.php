<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'min:1', 'max:500'],
        ];
    }
}
