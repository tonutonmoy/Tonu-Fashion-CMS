<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SettingRepository implements SettingRepositoryInterface
{
    private const CACHE_KEY = 'app_settings_all';

    public function __construct(private Setting $model) {}

    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, 3600, fn () => $this->model->newQuery()->get());
    }

    public function getByGroup(string $group): Collection
    {
        return $this->all()->where('group', $group);
    }

    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $setting = $this->all()->first(fn ($s) => $s->group === $group && $s->key === $key);

        if (! $setting) {
            return $default;
        }

        return $this->castValue($setting->value, $setting->type);
    }

    public function set(string $group, string $key, mixed $value, string $type = 'string'): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'type' => $type]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public function setMany(string $group, array $settings): void
    {
        foreach ($settings as $key => $data) {
            if (is_array($data)) {
                $this->set($group, $key, $data['value'], $data['type'] ?? 'string');
            } else {
                $this->set($group, $key, $data);
            }
        }
    }

    private function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value ?? '[]', true),
            default => $value,
        };
    }
}
