<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRestrictionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageCustomers() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,inactive'],
            'order_blocked' => ['sometimes', 'boolean'],
            'blog_blocked' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_blocked' => $this->boolean('order_blocked'),
            'blog_blocked' => $this->boolean('blog_blocked'),
        ]);
    }
}
