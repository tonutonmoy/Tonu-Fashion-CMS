<?php

namespace App\Http\Requests\Admin;

use App\Enums\ThemeSlug;
use App\Enums\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ThemeCustomizerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAdmin(AdminPermission::Settings) ?? false;
    }

    public function rules(): array
    {
        return [
            'active_theme' => ['required', Rule::enum(ThemeSlug::class)],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_family' => ['required', 'string', 'max:100'],
            'header_style' => ['required', Rule::in(array_keys(config('themes.header_styles')))],
            'footer_style' => ['required', Rule::in(array_keys(config('themes.footer_styles')))],
            'button_radius' => ['required', 'string', 'max:20'],
            'container_width' => ['required', 'string', 'max:20'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg,ico', 'max:1024'],
            'og_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach (['logo', 'favicon', 'og_image'] as $field) {
                $file = $this->file($field);
                if (! $file instanceof UploadedFile || $file->isValid()) {
                    continue;
                }

                $message = match ($file->getError()) {
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => sprintf(
                        'The %s is too large. Server limit is %s per file — use a smaller image or compress it before uploading.',
                        str_replace('_', ' ', $field),
                        ini_get('upload_max_filesize') ?: '2M'
                    ),
                    UPLOAD_ERR_PARTIAL => "The {$field} was only partially uploaded. Please try again.",
                    UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION => "Server could not store the {$field}. Check storage permissions.",
                    default => "The {$field} failed to upload. Please try again.",
                };

                $validator->errors()->add($field, $message);
            }
        });
    }

    public function messages(): array
    {
        return [
            'logo.max' => 'The logo must not be larger than 2 MB.',
            'favicon.max' => 'The favicon must not be larger than 1 MB.',
            'logo.mimes' => 'Logo must be PNG, JPG, WebP, GIF, or SVG.',
            'favicon.mimes' => 'Favicon must be PNG, JPG, ICO, WebP, GIF, or SVG.',
        ];
    }
}
