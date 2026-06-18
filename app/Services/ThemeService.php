<?php

namespace App\Services;

use App\Repositories\Contracts\ThemeSettingRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;

class ThemeService
{
    private ?string $activeTheme = null;

    public function __construct(
        private ThemeSettingRepositoryInterface $themeSettings
    ) {}

    public function activeSlug(): string
    {
        if (should_use_builder_draft()) {
            if (request()->filled('theme')) {
                $previewTheme = request()->query('theme');
                $themes = array_keys(config('themes.themes', []));
                if (in_array($previewTheme, $themes, true)) {
                    return $previewTheme;
                }
            }

            return $this->resolveActiveThemeSlug($this->settings()->active_theme);
        }

        if ($this->activeTheme === null) {
            $this->activeTheme = $this->themeSettings->get()->active_theme;
        }

        return $this->resolveActiveThemeSlug($this->activeTheme);
    }

    public function settings(): \App\Models\ThemeSetting
    {
        if (should_use_builder_draft()) {
            return app(BuilderPublishService::class)->getEffectiveThemeSettings();
        }

        return $this->themeSettings->get();
    }

    public function view(string $template, array $data = []): ViewInstance
    {
        $paths = $this->resolveViewPaths($template);

        foreach ($paths as $path) {
            if (View::exists($path)) {
                return view($path, $data);
            }
        }

        return view($paths[0], $data);
    }

    public function layout(): string
    {
        $layout = 'themes.'.$this->activeSlug().'.layouts.app';

        return View::exists($layout) ? $layout : 'layouts.frontend';
    }

    public function asset(string $path): string
    {
        $version = $this->settings()->asset_version;
        $theme = $this->activeSlug();
        $assetPath = "/themes/{$theme}/{$path}";

        if (File::exists(public_path(trim($assetPath, '/')))) {
            return asset($assetPath).'?v='.$version;
        }

        return asset('themes/'.$theme.'/'.$path).'?v='.$version;
    }

    public function cssVariables(): string
    {
        $s = $this->settings();

        return ":root {
            --theme-primary: {$s->primary_color};
            --theme-secondary: {$s->secondary_color};
            --theme-accent: {$s->accent_color};
            --theme-font: '{$s->font_family}', system-ui, sans-serif;
            --theme-btn-radius: {$s->button_radius};
            --theme-container-width: {$s->container_width};
        }
        body { font-family: var(--theme-font); }
        .theme-container { max-width: var(--theme-container-width, 80rem) !important; margin-left: auto; margin-right: auto; }
        .theme-btn, .theme-btn-primary, .btn-primary { border-radius: var(--theme-btn-radius, 0.5rem) !important; }
        .theme-btn-primary,
        .theme-btn-primary:hover,
        .theme-btn.theme-hero-btn,
        .theme-btn.theme-hero-btn:hover { color: #fff !important; }
        .theme-link:hover,
        .theme-nav a:hover { color: var(--theme-primary); }";
    }

    public function googleFontUrl(): string
    {
        $font = $this->settings()->font_family;
        $slug = config("themes.google_fonts.{$font}", str_replace(' ', '+', $font));

        return "https://fonts.googleapis.com/css2?family={$slug}:wght@400;500;600;700&display=swap";
    }

    public function jsonLd(): ?array
    {
        return $this->settings()->json_ld_schema;
    }

    public function defaultJsonLd(): array
    {
        $footer = app(FooterBuilderService::class)->get();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ClothingStore',
            'name' => $this->settings()->meta_title ?? setting('name', config('app.name')),
            'description' => $this->settings()->meta_description,
            'url' => url('/'),
            'telephone' => $footer->phone,
            'email' => $footer->email,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $footer->address,
                'addressCountry' => 'BD',
            ],
        ];
    }

    private function resolveActiveThemeSlug(string $slug): string
    {
        $themes = array_keys(config('themes.themes', []));

        return in_array($slug, $themes, true)
            ? $slug
            : config('themes.default');
    }

    private function resolveViewPaths(string $template): array
    {
        $slug = $this->activeSlug();
        $fallback = config('themes.default');

        return [
            "themes.{$slug}.{$template}",
            "themes.shared.{$template}",
            "themes.{$fallback}.{$template}",
            "frontend.{$template}",
        ];
    }
}
