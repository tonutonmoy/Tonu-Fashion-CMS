<?php

if (! function_exists('is_installed')) {
    function is_installed(): bool
    {
        return app(\App\Services\InstallerService::class)->isInstalled();
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\SettingService::class)->get($key, $default);
    }
}

if (! function_exists('format_bdt')) {
    function format_bdt(float|int|string $amount): string
    {
        return config('fashion.currency_symbol', '৳').number_format((float) $amount, 0);
    }
}

if (! function_exists('admin_per_page')) {
    function admin_per_page(): int
    {
        return (int) config('fashion.pagination.admin', 10);
    }
}

if (! function_exists('product_price')) {
    function product_price(\App\Models\Product $product): float
    {
        return (float) ($product->sale_price ?? $product->regular_price);
    }
}

if (! function_exists('theme')) {
    function theme(): \App\Services\ThemeService
    {
        return app(\App\Services\ThemeService::class);
    }
}

if (! function_exists('theme_view')) {
    function theme_view(string $template, array $data = []): \Illuminate\View\View
    {
        return theme()->view($template, $data);
    }
}

if (! function_exists('theme_asset')) {
    function theme_asset(string $path): string
    {
        return theme()->asset($path);
    }
}

if (! function_exists('theme_layout')) {
    function theme_layout(): string
    {
        return theme()->layout();
    }
}

if (! function_exists('license')) {
    function license(): ?\App\Models\License
    {
        return app(\App\Services\LicenseService::class)->current();
    }
}

if (! function_exists('license_valid')) {
    function license_valid(): bool
    {
        return app(\App\Services\LicenseService::class)->isValid();
    }
}

if (! function_exists('licensed_domain')) {
    function licensed_domain(): ?string
    {
        return app(\App\Services\LicenseService::class)->licensedDomain();
    }
}

if (! function_exists('image_url')) {
    function image_url(?string $path): ?string
    {
        return app(\App\Services\ImageService::class)->url($path);
    }
}

if (! function_exists('should_use_builder_draft')) {
    function should_use_builder_draft(): bool
    {
        if (! request()->boolean('preview')) {
            return false;
        }

        if (auth()->check() && auth()->user()->role->canManageSettings()) {
            return true;
        }

        $token = request()->query('_pv');
        if ($token && auth()->check()) {
            $expected = hash_hmac('sha256', (string) auth()->id(), (string) config('app.key'));

            return hash_equals($expected, (string) $token);
        }

        return false;
    }
}

if (! function_exists('hero_overlay_rgba')) {
    function hero_overlay_rgba(?string $color, float $opacity = 0.45): string
    {
        $hex = ltrim($color ?: '#000000', '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return "rgba(0, 0, 0, {$opacity})";
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$opacity})";
    }
}

if (! function_exists('hero_youtube_video_id')) {
    function hero_youtube_video_id(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/|youtube\.com/shorts/)([a-zA-Z0-9_-]{11})~', trim($url), $matches)) {
            return $matches[1];
        }

        return null;
    }
}

if (! function_exists('hero_youtube_thumbnail')) {
    function hero_youtube_thumbnail(?string $url): ?string
    {
        $id = hero_youtube_video_id($url);

        return $id ? "https://img.youtube.com/vi/{$id}/maxresdefault.jpg" : null;
    }
}

if (! function_exists('hero_video_embed_url')) {
    function hero_video_embed_url(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim($url);
        $id = hero_youtube_video_id($url);

        if ($id) {
            $origin = urlencode(url('/'));

            return "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&rel=0&modestbranding=1&playsinline=1&origin={$origin}";
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $matches)) {
            return "https://player.vimeo.com/video/{$matches[1]}?autoplay=1&muted=1&loop=1&background=1";
        }

        if (str_contains($url, 'youtube.com/embed') || str_contains($url, 'youtube-nocookie.com/embed') || str_contains($url, 'player.vimeo.com')) {
            return $url;
        }

        return $url;
    }
}

if (! function_exists('hero_typography_defaults')) {
    function hero_typography_defaults(): array
    {
        return config('themes.hero_typography_defaults', [
            'title_size' => 40,
            'subtitle_size' => 18,
            'button_size' => 14,
        ]);
    }
}

if (! function_exists('hero_typography_px')) {
    function hero_typography_px(mixed $value, string $key): int
    {
        $defaults = hero_typography_defaults();
        $default = (int) ($defaults[$key] ?? 40);

        if ($value === null || $value === '') {
            return $default;
        }

        $num = (float) $value;

        if ($num > 0 && $num < 10) {
            return (int) round($num * 16);
        }

        return (int) round(max(1, $num));
    }
}

if (! function_exists('hero_form_typography_value')) {
    function hero_form_typography_value(array $hero, string $key): int
    {
        if (old($key) !== null && old($key) !== '') {
            return hero_typography_px(old($key), $key);
        }

        if (isset($hero[$key]) && $hero[$key] !== '' && $hero[$key] !== null) {
            return hero_typography_px($hero[$key], $key);
        }

        return hero_typography_px(null, $key);
    }
}

if (! function_exists('hero_form_dimension_value')) {
    function hero_form_dimension_value(array $hero, string $key): string
    {
        $raw = old($key, $hero[$key] ?? null);

        if ($raw === null || $raw === '') {
            return '';
        }

        $num = (int) round((float) $raw);

        return $num > 0 ? (string) $num : '';
    }
}

if (! function_exists('hero_form_size_value')) {
    function hero_form_size_value(array $hero, string $key): int
    {
        if (old($key) !== null && old($key) !== '') {
            return hero_dimension_px(old($key), $key);
        }

        if (isset($hero[$key]) && $hero[$key] !== '' && $hero[$key] !== null) {
            return hero_dimension_px($hero[$key], $key);
        }

        return hero_dimension_px(null, $key);
    }
}

if (! function_exists('hero_content_layout_style')) {
    function hero_content_layout_style(string $layout): string
    {
        return match ($layout) {
            'left' => 'align-items:flex-start;justify-content:center;text-align:left;padding-left:clamp(1.5rem,6vw,5rem);',
            'right' => 'align-items:flex-end;justify-content:center;text-align:right;padding-right:clamp(1.5rem,6vw,5rem);',
            'bottom' => 'align-items:center;justify-content:flex-end;text-align:center;padding-bottom:clamp(2.5rem,8vw,4.5rem);',
            default => 'align-items:center;justify-content:center;text-align:center;',
        };
    }
}

if (! function_exists('hero_dimension_px')) {
    function hero_dimension_px(mixed $value, string $key): int
    {
        $defaults = config('themes.hero_size_defaults', [
            'button_width' => 0,
            'button_height' => 0,
        ]);
        $default = (int) ($defaults[$key] ?? 0);

        if ($value === null || $value === '') {
            return $default;
        }

        return (int) round(max(0, (float) $value));
    }
}

if (! function_exists('hero_button_dimension_css')) {
    function hero_button_dimension_css(mixed $value, string $key): string
    {
        $num = hero_dimension_px($value, $key);

        return $num > 0 ? $num.'px' : 'auto';
    }
}

if (! function_exists('hero_slide_style_vars')) {
    function hero_slide_style_vars(object $slide): string
    {
        return implode('; ', [
            '--hero-title-size:'.hero_typography_px($slide->title_size ?? null, 'title_size').'px',
            '--hero-subtitle-size:'.hero_typography_px($slide->subtitle_size ?? null, 'subtitle_size').'px',
            '--hero-button-size:'.hero_typography_px($slide->button_size ?? null, 'button_size').'px',
            '--hero-button-width:'.hero_button_dimension_css($slide->button_width ?? null, 'button_width'),
            '--hero-button-height:'.hero_button_dimension_css($slide->button_height ?? null, 'button_height'),
        ]).';';
    }
}

if (! function_exists('hero_video_is_file')) {
    function hero_video_is_file(?string $url): bool
    {
        if (! filled($url)) {
            return false;
        }

        $path = strtolower(parse_url($url, PHP_URL_PATH) ?? '');

        return (bool) preg_match('~\.(mp4|webm|ogg)$~', $path);
    }
}

if (! function_exists('builder_preview_url')) {
    function builder_preview_url(?string $path = null, ?string $section = null, string $device = 'desktop', ?string $theme = null): string
    {
        $url = $path ? url($path) : route('home');
        $params = [
            'preview' => '1',
            'device' => $device,
        ];
        if ($theme) {
            $params['theme'] = $theme;
        }
        if (auth()->check() && auth()->user()->role->canManageSettings()) {
            $params['_pv'] = hash_hmac('sha256', (string) auth()->id(), (string) config('app.key'));
        }
        $url .= (str_contains($url, '?') ? '&' : '?').http_build_query($params);

        if ($section) {
            $url .= '#'.ltrim($section, '#');
        }

        return $url;
    }
}

if (! function_exists('trans_field')) {
    function trans_field(?object $model, string $field, ?string $locale = null): mixed
    {
        if (! $model) {
            return null;
        }

        if (method_exists($model, 'translated')) {
            return $model->translated($field, $locale);
        }

        return $model->{$field} ?? null;
    }
}

if (! function_exists('filter_storefront_menu')) {
    function filter_storefront_menu($items)
    {
        if (! $items || (is_countable($items) && count($items) === 0)) {
            return $items;
        }

        $collection = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);

        $blockedTitles = ['orders', 'order', 'profile', 'login', 'register', 'track order', 'tracking', 'my account', 'account'];
        $blockedPaths = ['/orders', '/profile', '/login', '/register', '/track-order'];

        return $collection
            ->filter(function ($item) use ($blockedTitles, $blockedPaths) {
                $title = strtolower(trim((string) ($item->title ?? '')));

                foreach ($blockedTitles as $blocked) {
                    if ($title === $blocked || str_contains($title, $blocked)) {
                        return false;
                    }
                }

                try {
                    $url = strtolower((string) ($item->resolvedUrl() ?? ''));
                    foreach ($blockedPaths as $path) {
                        if (str_contains($url, $path)) {
                            return false;
                        }
                    }
                } catch (\Throwable) {
                    // ignore broken menu URLs
                }

                return true;
            })
            ->values()
            ->map(function ($item) {
                if ($item->relationLoaded('children') && $item->children->isNotEmpty()) {
                    $item->setRelation('children', filter_storefront_menu($item->children));
                }

                return $item;
            });
    }
}
