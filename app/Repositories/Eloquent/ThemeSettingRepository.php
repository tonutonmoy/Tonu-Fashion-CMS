<?php

namespace App\Repositories\Eloquent;

use App\Models\ThemeSetting;
use App\Repositories\Contracts\ThemeSettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ThemeSettingRepository implements ThemeSettingRepositoryInterface
{
    private const CACHE_KEY = 'theme_settings';

    public function __construct(private ThemeSetting $model) {}

    public function get(): ThemeSetting
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return $this->model->newQuery()->firstOrCreate([], [
                'active_theme' => config('themes.default'),
                'primary_color' => '#e11d48',
                'secondary_color' => '#1f2937',
                'font_family' => 'Inter',
                'header_style' => 'default',
                'footer_style' => 'default',
                'asset_version' => '1.0.0',
            ]);
        });
    }

    public function update(array $data): ThemeSetting
    {
        $settings = $this->get();
        $settings->update($data);
        Cache::forget(self::CACHE_KEY);

        return $settings->fresh();
    }

    public function bumpAssetVersion(): ThemeSetting
    {
        $settings = $this->get();
        $version = (float) $settings->asset_version;
        $settings->update(['asset_version' => number_format($version + 0.1, 1)]);
        Cache::forget(self::CACHE_KEY);

        return $settings->fresh();
    }
}
