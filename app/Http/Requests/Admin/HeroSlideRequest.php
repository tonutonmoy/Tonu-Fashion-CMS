<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class HeroSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $videoUrl = $this->normalizeUrl($this->input('video_url'));
        $overlay = $this->input('overlay_color');
        $layout = $this->input('content_layout', 'centered');
        $layouts = array_keys(config('themes.hero_content_layouts', []));

        $this->merge([
            'video_url' => $videoUrl,
            'subtitle' => $this->filled('subtitle') ? $this->input('subtitle') : null,
            'button_text' => $this->filled('button_text') ? $this->input('button_text') : null,
            'button_link' => $this->filled('button_link') ? $this->input('button_link') : null,
            'overlay_color' => filled($overlay) ? $overlay : null,
            'content_layout' => in_array($layout, $layouts, true) ? $layout : 'centered',
            'title_size' => $this->nullableSize($this->input('title_size')),
            'subtitle_size' => $this->nullableSize($this->input('subtitle_size')),
            'button_size' => $this->nullableSize($this->input('button_size')),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:500'],
            'desktop_image' => ['nullable', 'image', 'max:8192'],
            'mobile_image' => ['nullable', 'image', 'max:8192'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'overlay_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'content_layout' => ['nullable', 'in:centered,left,right,bottom'],
            'title_size' => ['nullable', 'numeric', 'min:1', 'max:6'],
            'subtitle_size' => ['nullable', 'numeric', 'min:0.75', 'max:3'],
            'button_size' => ['nullable', 'numeric', 'min:0.75', 'max:2'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Slide title is required.',
            'desktop_image.image' => 'Desktop image must be a valid image file (JPG, PNG, WebP).',
            'mobile_image.image' => 'Mobile image must be a valid image file (JPG, PNG, WebP).',
            'desktop_image.max' => 'Desktop image may not be larger than 8 MB.',
            'mobile_image.max' => 'Mobile image may not be larger than 8 MB.',
            'video_url.url' => 'Video URL must be a valid link (include https:// or paste youtube.com/...).',
            'status.required' => 'Please choose Active or Inactive.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->isMethod('post') && ! $this->hasFile('desktop_image') && ! $this->hasFile('mobile_image') && blank($this->input('video_url'))) {
                // Slides without media still work — placeholder gradient is shown.
            }
        });
    }

    private function normalizeUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim($url);

        if (! preg_match('~^https?://~i', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }

    private function nullableSize(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
