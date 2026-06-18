<?php

namespace App\Services;

use App\Repositories\Contracts\ThemeSettingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ThemeCustomizerService
{
    public function __construct(
        private ThemeSettingRepositoryInterface $themeSettings,
        private BuilderPublishService $publish,
        private ImageService $images
    ) {}

    public function get(): \App\Models\ThemeSetting
    {
        return $this->publish->getEffectiveThemeSettings();
    }

    public function update(array $data, ?UploadedFile $logo = null, ?UploadedFile $favicon = null, ?UploadedFile $ogImage = null): \App\Models\ThemeSetting
    {
        $current = $this->get();

        if ($logo) {
            $this->images->delete($current->logo);
            $path = $this->images->upload($logo, 'theme', 800);
            if (blank($path)) {
                throw ValidationException::withMessages([
                    'logo' => 'Logo upload failed. Check storage permissions or try a PNG/JPG file.',
                ]);
            }
            $data['logo'] = $path;
        }

        if ($favicon) {
            $this->images->delete($current->favicon);
            $path = $this->images->upload($favicon, 'theme', 128, 90, false);
            if (blank($path)) {
                throw ValidationException::withMessages([
                    'favicon' => 'Favicon upload failed. Use PNG, JPG, ICO, or SVG (max 1MB).',
                ]);
            }
            $data['favicon'] = $path;
        }

        if ($ogImage) {
            $this->images->delete($current->og_image);
            $path = $this->images->upload($ogImage, 'theme', 1200);
            if (blank($path)) {
                throw ValidationException::withMessages([
                    'og_image' => 'OG image upload failed. Please try again.',
                ]);
            }
            $data['og_image'] = $path;
        }

        $this->publish->setDraftTheme($data);

        return $this->get();
    }

    public function availableThemes(): array
    {
        return config('themes.themes', []);
    }

    public function globalDefaults(): array
    {
        return config('themes.global_defaults', []);
    }

    public function defaultsForTheme(string $slug): array
    {
        return config("themes.themes.{$slug}.defaults", $this->globalDefaults());
    }

    public function reset(string $type = 'all'): \App\Models\ThemeSetting
    {
        $current = $this->get();

        if ($type === 'colors') {
            $defaults = $this->defaultsForTheme($current->active_theme);

            $this->publish->setDraftTheme([
                'primary_color' => $defaults['primary_color'],
                'secondary_color' => $defaults['secondary_color'],
                'accent_color' => $defaults['accent_color'],
            ]);

            return $this->get();
        }

        if ($type === 'theme') {
            $slug = $current->active_theme;
            $defaults = $this->defaultsForTheme($slug);

            $this->publish->setDraftTheme(array_merge($defaults, [
                'active_theme' => $slug,
            ]));

            return $this->get();
        }

        $defaults = $this->globalDefaults();
        $this->images->delete($current->logo);
        $this->images->delete($current->favicon);

        $this->publish->setDraftTheme(array_merge($defaults, [
            'logo' => null,
            'favicon' => null,
        ]));

        return $this->get();
    }
}
