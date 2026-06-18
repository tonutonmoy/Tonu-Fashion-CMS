<?php

use App\Models\HeroSlide;
use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $section = HomepageSection::query()->where('section_key', 'hero_slider')->first();

        if (! $section) {
            return;
        }

        $settings = $section->settings ?? [];

        if (! empty($settings['media'])) {
            return;
        }

        $slides = HeroSlide::query()->orderBy('sort_order')->get();

        if ($slides->isEmpty()) {
            $section->update([
                'settings' => array_merge($this->defaultHeroSettings(), $settings),
            ]);

            return;
        }

        $first = $slides->first();
        $media = $slides->map(function (HeroSlide $slide, int $index) {
            if (filled($slide->video_url)) {
                return [
                    'id' => (string) Str::uuid(),
                    'type' => 'video',
                    'video_url' => $slide->video_url,
                    'sort_order' => $index + 1,
                ];
            }

            return [
                'id' => (string) Str::uuid(),
                'type' => 'image',
                'desktop_image' => $slide->desktop_image,
                'mobile_image' => $slide->mobile_image,
                'sort_order' => $index + 1,
            ];
        })->values()->all();

        $section->update([
            'settings' => array_merge($this->defaultHeroSettings(), $settings, [
                'title' => $first->title,
                'subtitle' => $first->subtitle,
                'button_text' => $first->button_text,
                'button_link' => $first->button_link,
                'content_layout' => $first->content_layout ?? 'centered',
                'title_size' => $first->title_size,
                'subtitle_size' => $first->subtitle_size,
                'button_size' => $first->button_size,
                'overlay_color' => $first->overlay_color,
                'autoplay_seconds' => 5,
                'media' => $media,
            ]),
        ]);
    }

    public function down(): void
    {
        // Data migration — no rollback.
    }

    private function defaultHeroSettings(): array
    {
        return [
            'title' => '',
            'subtitle' => null,
            'button_text' => null,
            'button_link' => null,
            'content_layout' => 'centered',
            'title_size' => 2.5,
            'subtitle_size' => 1.125,
            'button_size' => 0.875,
            'overlay_color' => '#000000',
            'autoplay_seconds' => 5,
            'media' => [],
        ];
    }
};
