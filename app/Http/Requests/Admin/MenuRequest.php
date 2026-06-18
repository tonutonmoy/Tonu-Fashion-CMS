<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAdmin(AdminPermission::Cms) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('items_json') && is_string($this->items_json)) {
            $decoded = json_decode($this->items_json, true);
            $this->merge(['items' => is_array($decoded) ? $decoded : []]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.title' => ['required_with:items', 'string', 'max:255'],
            'items.*.url' => ['nullable', 'string', 'max:500'],
            'items.*.page_id' => ['nullable', 'integer', 'exists:cms_pages,id'],
            'items.*.open_in_new_tab' => ['nullable', 'boolean'],
            'items.*.children' => ['nullable', 'array'],
        ];
    }
}
