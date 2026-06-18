<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HeroBuilderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role->canManageSettings() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $layouts = array_keys(config('themes.hero_content_layouts', []));
        $layout = $this->input('content_layout', 'centered');

        $this->merge([
            'subtitle' => $this->filled('subtitle') ? $this->input('subtitle') : null,
            'button_text' => $this->filled('button_text') ? $this->input('button_text') : null,
            'button_link' => $this->filled('button_link') ? $this->input('button_link') : null,
            'show_title' => $this->boolean('show_title'),
            'show_subtitle' => $this->boolean('show_subtitle'),
            'show_button' => $this->boolean('show_button'),
            'overlay_color' => filled($this->input('overlay_color')) ? $this->input('overlay_color') : '#000000',
            'content_layout' => in_array($layout, $layouts, true) ? $layout : 'centered',
            'autoplay_seconds' => (int) ($this->input('autoplay_seconds', 5) ?: 5),
            'title_size' => hero_typography_px($this->input('title_size'), 'title_size'),
            'subtitle_size' => hero_typography_px($this->input('subtitle_size'), 'subtitle_size'),
            'button_size' => hero_typography_px($this->input('button_size'), 'button_size'),
            'button_width' => hero_dimension_px($this->filled('button_width') ? $this->input('button_width') : null, 'button_width'),
            'button_height' => hero_dimension_px($this->filled('button_height') ? $this->input('button_height') : null, 'button_height'),
            'remove_media' => array_values(array_filter((array) $this->input('remove_media', []))),
            'media_order' => array_values(array_filter((array) $this->input('media_order', []))),
            'video_url' => filled($this->input('video_url')) ? trim($this->input('video_url')) : null,
            'media_video' => (array) $this->input('media_video', []),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:500'],
            'show_title' => ['nullable', 'boolean'],
            'show_subtitle' => ['nullable', 'boolean'],
            'show_button' => ['nullable', 'boolean'],
            'content_layout' => ['nullable', 'in:centered,left,right,bottom'],
            'title_size' => ['nullable', 'integer', 'min:12', 'max:160'],
            'subtitle_size' => ['nullable', 'integer', 'min:10', 'max:96'],
            'button_size' => ['nullable', 'integer', 'min:8', 'max:48'],
            'button_width' => ['nullable', 'integer', 'min:0', 'max:600'],
            'button_height' => ['nullable', 'integer', 'min:0', 'max:200'],
            'overlay_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'autoplay_seconds' => ['nullable', 'integer', 'min:3', 'max:30'],
            'media_images' => ['nullable', 'array', 'max:'.config('uploads.hero_max_files', 20)],
            'media_images.*' => ['image', 'max:'.config('uploads.hero_image_max_kb', 16384)],
            'media_video' => ['nullable', 'array'],
            'media_video.*' => ['nullable', 'string', 'max:500'],
            'media_replace' => ['nullable', 'array'],
            'media_replace.*' => ['nullable', 'image', 'max:'.config('uploads.hero_image_max_kb', 16384)],
            'video_url' => ['nullable', 'string', 'max:500'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['string', 'max:64'],
            'media_order' => ['nullable', 'array'],
            'media_order.*' => ['string', 'max:64'],
        ];
    }
}
