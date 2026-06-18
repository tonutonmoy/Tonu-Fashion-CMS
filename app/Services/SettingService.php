<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function __construct(
        private SettingRepositoryInterface $settings
    ) {}

    public function get(string $key, mixed $default = null, string $group = 'store'): mixed
    {
        return $this->settings->get($group, $key, $default);
    }

    public function getGroup(string $group): array
    {
        return $this->settings->getByGroup($group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function updateStore(array $data): void
    {
        $this->settings->setMany('store', $data);
        Cache::forget('app_settings_all');
    }

    public function updateMarketing(array $data): void
    {
        $this->settings->setMany('marketing', $data);
        Cache::forget('app_settings_all');
    }

    public function getMarketing(): array
    {
        return [
            'facebook_pixel_id' => $this->settings->get('marketing', 'facebook_pixel_id'),
            'facebook_capi_token' => $this->settings->get('marketing', 'facebook_capi_token'),
            'facebook_dataset_id' => $this->settings->get('marketing', 'facebook_dataset_id'),
            'google_analytics_id' => $this->settings->get('marketing', 'google_analytics_id'),
            'google_tag_manager_id' => $this->settings->get('marketing', 'google_tag_manager_id'),
            'tiktok_pixel_id' => $this->settings->get('marketing', 'tiktok_pixel_id'),
        ];
    }
}
