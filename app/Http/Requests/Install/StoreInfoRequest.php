<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $themes = array_keys(config('themes.themes', []));

        return [
            'store_name' => ['required', 'string', 'max:255'],
            'store_email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'currency_code' => ['required', 'string', 'max:3'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'timezone' => ['required', 'string', 'max:64'],
            'default_theme' => ['required', 'string', Rule::in($themes)],
            'app_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
