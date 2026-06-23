<?php

namespace App\Services;

use App\Enums\CourierType;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CourierSettingsService
{
    private const CACHE_KEY = 'courier_settings';

    public function __construct(private SettingRepositoryInterface $settings) {}

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $data = [
                'default_courier' => $this->settings->get('courier', 'default_courier', CourierType::Steadfast->value),
                'auto_parcel_enabled' => (bool) $this->settings->get('courier', 'auto_parcel_enabled', false),
            ];

            foreach (CourierType::cases() as $courier) {
                $key = $courier->value;
                $data[$key] = $this->courierFromStorage($key);
            }

            return $data;
        });
    }

    public function courier(string $courier): array
    {
        return $this->all()[$courier] ?? $this->courierFromStorage($courier);
    }

    public function update(array $data): void
    {
        $flat = [
            'default_courier' => $data['default_courier'] ?? CourierType::Steadfast->value,
            'auto_parcel_enabled' => (bool) ($data['auto_parcel_enabled'] ?? false),
        ];

        foreach (CourierType::cases() as $courier) {
            $key = $courier->value;
            $flat["{$key}_enabled"] = (bool) ($data["{$key}_enabled"] ?? false);
            $flat["{$key}_api_key"] = $data["{$key}_api_key"] ?? '';
            $flat["{$key}_secret_key"] = $data["{$key}_secret_key"] ?? '';
            $flat["{$key}_base_url"] = $data["{$key}_base_url"] ?? config("couriers.{$key}.default_base_url");
        }

        $this->settings->setMany('courier', $flat);
        Cache::forget(self::CACHE_KEY);
        Cache::forget('app_settings_all');
    }

    public function defaultCourier(): CourierType
    {
        return CourierType::tryFrom($this->all()['default_courier'] ?? '') ?? CourierType::Steadfast;
    }

    public function isAutoParcelEnabled(): bool
    {
        return (bool) ($this->all()['auto_parcel_enabled'] ?? false);
    }

    public function enabledCouriers(): array
    {
        return collect(CourierType::cases())
            ->filter(fn (CourierType $type) => $this->courier($type->value)['enabled'] ?? false)
            ->values()
            ->all();
    }

    /** @return array<int, array{type: CourierType, label: string}> */
    public function activeConfiguredCouriers(CourierManager $manager): array
    {
        return collect($this->enabledCouriers())
            ->filter(fn (CourierType $type) => $manager->gateway($type)->isConfigured())
            ->map(fn (CourierType $type) => [
                'type' => $type,
                'label' => $type->label(),
            ])
            ->values()
            ->all();
    }

    private function courierFromStorage(string $key): array
    {
        return [
            'enabled' => (bool) $this->settings->get('courier', "{$key}_enabled", false),
            'api_key' => $this->settings->get('courier', "{$key}_api_key"),
            'secret_key' => $this->settings->get('courier', "{$key}_secret_key"),
            'base_url' => $this->settings->get('courier', "{$key}_base_url") ?: config("couriers.{$key}.default_base_url"),
        ];
    }
}
