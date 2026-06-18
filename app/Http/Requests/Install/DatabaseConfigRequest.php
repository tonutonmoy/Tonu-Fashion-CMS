<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;

class DatabaseConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'db_driver' => ['nullable', 'in:mongodb'],
            'mongodb_uri' => ['required', 'string', 'max:2048'],
            'db_database' => ['required', 'string', 'max:255'],
        ];
    }
}
