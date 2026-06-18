<?php

namespace App\Http\Requests\Admin;

use App\Enums\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAdmin(AdminPermission::Cms) ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:8192', 'mimes:jpeg,jpg,png,webp,gif,svg,pdf'],
            'folder' => ['nullable', 'string', Rule::in(config('cms.media_folders', []))],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'folder' => $this->input('folder', 'uploads'),
        ]);
    }
}
