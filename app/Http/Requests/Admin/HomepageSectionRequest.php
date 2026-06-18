<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomepageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    public function rules(): array
    {
        return [
            'enabled' => ['sometimes', 'boolean'],
            'title' => ['sometimes', 'string', 'max:255'],
            'settings' => ['sometimes', 'array'],
            'settings.limit' => ['nullable', 'integer', 'min:1', 'max:24'],
            'settings.category_ids' => ['nullable', 'array'],
            'settings.category_ids.*' => ['integer', 'exists:categories,id'],
            'settings.product_ids' => ['nullable', 'array'],
            'settings.product_ids.*' => ['integer', 'exists:products,id'],
            'settings.start_date' => ['nullable', 'date'],
            'settings.end_date' => ['nullable', 'date', 'after:settings.start_date'],
            'settings.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'settings.show_countdown' => ['nullable', 'boolean'],
            'settings.title' => ['nullable', 'string', 'max:255'],
            'settings.subtitle' => ['nullable', 'string', 'max:500'],
            'settings.items' => ['nullable', 'array'],
            'settings.posts' => ['nullable', 'array'],
        ];
    }
}
