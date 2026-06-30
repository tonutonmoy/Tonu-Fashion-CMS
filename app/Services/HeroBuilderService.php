<?php

namespace App\Services;

use App\Enums\HomepageSectionKey;
use App\Models\HomepageSection;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class HeroBuilderService
{
    public function __construct(
        private HomepageSectionRepositoryInterface $sections,
        private BuilderPublishService $publish,
        private HeroSlideRepositoryInterface $slides,
        private ImageService $images,
    ) {}

    public function getConfig(): array
    {
        return $this->configFromSettings($this->heroSettings());
    }

    public function configFromSettings(array $settings): array
    {
        if (! array_key_exists('media', $settings)) {
            $legacy = $this->legacyMediaFromSlides();
            if (! empty($legacy)) {
                $settings['media'] = $legacy;
            }
        }

        return $this->normalizeConfig($settings);
    }

    public function update(
        array $data,
        array $uploadedImages = [],
        ?string $videoUrl = null,
        array $removeMediaIds = [],
        ?array $mediaOrder = null,
        array $replaceImages = [],
        array $mediaVideoUpdates = [],
    ): array {
        $section = $this->getHeroSection();

        if (! $section) {
            return $this->getConfig();
        }

        $settings = $this->normalizeConfig($this->heroSettings());
        $media = collect($settings['media']);

        foreach ($removeMediaIds as $mediaId) {
            $item = $media->firstWhere('id', $mediaId);
            if ($item && ($item['type'] ?? '') === 'image') {
                $this->images->delete($item['desktop_image'] ?? null);
                $this->images->delete($item['mobile_image'] ?? null);
            }
            $media = $media->reject(fn ($row) => ($row['id'] ?? '') === $mediaId);
        }

        foreach ($uploadedImages as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $this->images->upload($file, 'hero', 1400);
            if (blank($path)) {
                continue;
            }

            $media->push([
                'id' => (string) Str::uuid(),
                'type' => 'image',
                'desktop_image' => $path,
                'mobile_image' => null,
                'sort_order' => ($media->max('sort_order') ?? 0) + 1,
            ]);
        }

        foreach ($replaceImages as $mediaId => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $item = $media->firstWhere('id', $mediaId);
            if (! $item || ($item['type'] ?? '') !== 'image') {
                continue;
            }

            $path = $this->images->upload($file, 'hero', 1400);
            if (blank($path)) {
                continue;
            }

            $this->images->delete($item['desktop_image'] ?? null);
            $this->images->delete($item['mobile_image'] ?? null);

            $media = $media->map(function ($row) use ($mediaId, $path) {
                if (($row['id'] ?? '') !== $mediaId) {
                    return $row;
                }

                $row['desktop_image'] = $path;
                $row['mobile_image'] = null;

                return $row;
            });
        }

        foreach ($mediaVideoUpdates as $mediaId => $url) {
            $url = trim((string) $url);
            if ($url === '') {
                continue;
            }

            $media = $media->map(function ($row) use ($mediaId, $url) {
                if (($row['id'] ?? '') !== $mediaId || ($row['type'] ?? '') !== 'video') {
                    return $row;
                }

                $row['video_url'] = $this->normalizeUrl($url);

                return $row;
            });
        }

        if (filled($videoUrl)) {
            $normalized = $this->normalizeUrl($videoUrl);
            $exists = $media->contains(
                fn ($row) => ($row['type'] ?? '') === 'video' && ($row['video_url'] ?? '') === $normalized
            );

            if (! $exists) {
                $media->push([
                    'id' => (string) Str::uuid(),
                    'type' => 'video',
                    'video_url' => $normalized,
                    'sort_order' => ($media->max('sort_order') ?? 0) + 1,
                ]);
            }
        }

        $media = $this->applyMediaOrder($media, $mediaOrder);

        $settings = array_merge($settings, [
            'title' => $data['title'] ?? $settings['title'] ?? '',
            'subtitle' => $data['subtitle'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_link' => $data['button_link'] ?? null,
            'show_title' => array_key_exists('show_title', $data) ? (bool) $data['show_title'] : ($settings['show_title'] ?? true),
            'show_subtitle' => array_key_exists('show_subtitle', $data) ? (bool) $data['show_subtitle'] : ($settings['show_subtitle'] ?? true),
            'show_button' => array_key_exists('show_button', $data) ? (bool) $data['show_button'] : ($settings['show_button'] ?? true),
            'content_layout' => $data['content_layout'] ?? 'centered',
            'title_size' => hero_typography_px($data['title_size'] ?? null, 'title_size'),
            'subtitle_size' => hero_typography_px($data['subtitle_size'] ?? null, 'subtitle_size'),
            'button_size' => hero_typography_px($data['button_size'] ?? null, 'button_size'),
            'button_width' => hero_dimension_px($data['button_width'] ?? null, 'button_width'),
            'button_height' => hero_dimension_px($data['button_height'] ?? null, 'button_height'),
            'overlay_color' => $data['overlay_color'] ?? ($settings['overlay_color'] ?? '#000000'),
            'autoplay_seconds' => (int) ($data['autoplay_seconds'] ?? $settings['autoplay_seconds'] ?? 5),
            'media' => $media->sortBy('sort_order')->values()->all(),
        ]);

        $this->publish->updateHomepageSection($section->id, [
            'settings' => $settings,
        ]);

        $this->publish->clearHeroSlidesDraft();

        return $this->normalizeConfig($settings);
    }

    private function applyMediaOrder($media, ?array $mediaOrder)
    {
        if (empty($mediaOrder)) {
            return $media->values()->map(function ($row, $index) {
                $row['sort_order'] = $index + 1;

                return $row;
            });
        }

        $keyed = $media->keyBy('id');
        $ordered = collect();
        $used = [];

        foreach ($mediaOrder as $order => $id) {
            if (! $keyed->has($id)) {
                continue;
            }

            $row = $keyed->get($id);
            $row['sort_order'] = $order + 1;
            $ordered->push($row);
            $used[] = $id;
        }

        $keyed->each(function ($row, $id) use ($ordered, &$used) {
            if (in_array($id, $used, true)) {
                return;
            }

            $row['sort_order'] = ($ordered->max('sort_order') ?? 0) + 1;
            $ordered->push($row);
            $used[] = $id;
        });

        return $ordered;
    }

    public function getHeroSection(): ?HomepageSection
    {
        if (should_use_builder_draft() || request()->routeIs('admin.theme.hero-slides')) {
            return $this->publish->getEffectiveHomepageSections()
                ->firstWhere('section_key', HomepageSectionKey::HeroSlider->value);
        }

        return $this->sections->findByKey(HomepageSectionKey::HeroSlider->value);
    }

    private function heroSettings(): array
    {
        return $this->getHeroSection()?->settings ?? [];
    }

    private function legacyMediaFromSlides(): array
    {
        $slides = should_use_builder_draft()
            ? $this->publish->getEffectiveHeroSlides()
            : $this->slides->getActiveOrdered();

        return $slides->values()->map(function ($slide, int $index) {
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
        })->all();
    }

    private function normalizeConfig(array $settings): array
    {
        $layouts = array_keys(config('themes.hero_content_layouts', []));
        $layout = $settings['content_layout'] ?? config('themes.hero_content_defaults.content_layout', 'centered');
        $copyDefaults = config('themes.hero_content_defaults', []);

        return [
            'title' => $settings['title'] ?? ($copyDefaults['title'] ?? ''),
            'subtitle' => $settings['subtitle'] ?? ($copyDefaults['subtitle'] ?? null),
            'button_text' => $settings['button_text'] ?? ($copyDefaults['button_text'] ?? null),
            'button_link' => $settings['button_link'] ?? ($copyDefaults['button_link'] ?? null),
            'show_title' => array_key_exists('show_title', $settings) ? (bool) $settings['show_title'] : ($copyDefaults['show_title'] ?? true),
            'show_subtitle' => array_key_exists('show_subtitle', $settings) ? (bool) $settings['show_subtitle'] : ($copyDefaults['show_subtitle'] ?? true),
            'show_button' => array_key_exists('show_button', $settings) ? (bool) $settings['show_button'] : ($copyDefaults['show_button'] ?? true),
            'content_layout' => in_array($layout, $layouts, true) ? $layout : 'centered',
            'title_size' => hero_typography_px($settings['title_size'] ?? null, 'title_size'),
            'subtitle_size' => hero_typography_px($settings['subtitle_size'] ?? null, 'subtitle_size'),
            'button_size' => hero_typography_px($settings['button_size'] ?? null, 'button_size'),
            'button_width' => hero_dimension_px($settings['button_width'] ?? null, 'button_width'),
            'button_height' => hero_dimension_px($settings['button_height'] ?? null, 'button_height'),
            'overlay_color' => $settings['overlay_color'] ?? '#000000',
            'autoplay_seconds' => max(3, min(30, (int) ($settings['autoplay_seconds'] ?? 5))),
            'media' => collect($settings['media'] ?? [])
                ->sortBy('sort_order')
                ->values()
                ->all(),
        ];
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);

        if (! preg_match('~^https?://~i', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }
}
