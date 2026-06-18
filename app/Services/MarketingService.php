<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class MarketingService
{
    private const CACHE_KEY = 'marketing_settings';

    public function __construct(private SettingRepositoryInterface $settings) {}

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, fn () => [
            'facebook_pixel_id' => $this->settings->get('marketing', 'facebook_pixel_id'),
            'facebook_access_token' => $this->settings->get('marketing', 'facebook_access_token')
                ?? $this->settings->get('marketing', 'facebook_capi_token'),
            'facebook_dataset_id' => $this->settings->get('marketing', 'facebook_dataset_id'),
            'test_event_code' => $this->settings->get('marketing', 'test_event_code'),
            'ga_measurement_id' => $this->settings->get('marketing', 'ga_measurement_id')
                ?? $this->settings->get('marketing', 'google_analytics_id'),
            'gtm_id' => $this->settings->get('marketing', 'gtm_id')
                ?? $this->settings->get('marketing', 'google_tag_manager_id'),
            'tiktok_pixel_id' => $this->settings->get('marketing', 'tiktok_pixel_id'),
        ]);
    }

    public function update(array $data): void
    {
        $this->settings->setMany('marketing', $data);
        Cache::forget(self::CACHE_KEY);
        Cache::forget('app_settings_all');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function isFacebookEnabled(): bool
    {
        return (bool) $this->get('facebook_pixel_id');
    }

    public function isCapiEnabled(): bool
    {
        return $this->isFacebookEnabled()
            && $this->get('facebook_access_token')
            && $this->get('facebook_dataset_id');
    }
}
