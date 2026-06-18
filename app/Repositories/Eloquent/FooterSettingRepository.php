<?php

namespace App\Repositories\Eloquent;

use App\Models\FooterSetting;
use App\Repositories\Contracts\FooterSettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class FooterSettingRepository implements FooterSettingRepositoryInterface
{
    private const CACHE_KEY = 'footer_settings';

    public function __construct(private FooterSetting $model) {}

    public function get(): FooterSetting
    {
        return Cache::remember(self::CACHE_KEY, 3600, fn () => $this->model->newQuery()->firstOrCreate([]));
    }

    public function update(array $data): FooterSetting
    {
        $settings = $this->get();
        $settings->update($data);
        Cache::forget(self::CACHE_KEY);

        return $settings->fresh();
    }
}
