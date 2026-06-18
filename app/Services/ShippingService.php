<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ShippingService
{
    private const CACHE_KEY = 'shipping_settings';

    public function __construct(private SettingRepositoryInterface $settings) {}

    public function settings(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, fn () => [
            'inside_dhaka' => (float) ($this->settings->get('shipping', 'inside_dhaka') ?? config('fashion.shipping.inside_dhaka', 80)),
            'outside_dhaka' => (float) ($this->settings->get('shipping', 'outside_dhaka') ?? config('fashion.shipping.outside_dhaka', 150)),
            'free_shipping_limit' => (float) ($this->settings->get('shipping', 'free_shipping_limit') ?? config('fashion.shipping.free_shipping_limit', 2000)),
        ]);
    }

    public function update(array $data): void
    {
        $this->settings->setMany('shipping', $data);
        Cache::forget(self::CACHE_KEY);
        Cache::forget('app_settings_all');
    }

    public function calculate(float $subtotal, string $division, ?string $district = null): float
    {
        $config = $this->settings();

        if ($subtotal >= $config['free_shipping_limit']) {
            return 0;
        }

        if ($this->isInsideDhaka($division, $district)) {
            return $config['inside_dhaka'];
        }

        return $config['outside_dhaka'];
    }

    public function cartHasFreeDeliveryProduct(Collection $items): bool
    {
        return $items->contains(fn ($item) => (bool) $item->product?->free_delivery);
    }

    public function cartRequiresShippingSelection(Collection $items, float $subtotal): bool
    {
        if ($this->cartHasFreeDeliveryProduct($items)) {
            return false;
        }

        return $subtotal < $this->settings()['free_shipping_limit'];
    }

    public function deliveryOptions(Collection $items, float $subtotal): array
    {
        if (! $this->cartRequiresShippingSelection($items, $subtotal)) {
            return [];
        }

        $config = $this->settings();

        return [
            [
                'id' => 'inside_dhaka',
                'label' => __('common.inside_dhaka'),
                'price' => $config['inside_dhaka'],
            ],
            [
                'id' => 'outside_dhaka',
                'label' => __('common.outside_dhaka'),
                'price' => $config['outside_dhaka'],
            ],
        ];
    }

    public function freeDeliveryReason(Collection $items, float $subtotal): ?string
    {
        if ($this->cartHasFreeDeliveryProduct($items)) {
            return __('common.free_delivery_product');
        }

        if ($subtotal >= $this->settings()['free_shipping_limit']) {
            return __('common.free_delivery_threshold', [
                'amount' => format_bdt($this->settings()['free_shipping_limit']),
            ]);
        }

        return null;
    }

    public function calculateForCart(Collection $items, float $subtotal, ?string $deliveryZone = null): float
    {
        if (! $this->cartRequiresShippingSelection($items, $subtotal)) {
            return 0.0;
        }

        $config = $this->settings();

        return $deliveryZone === 'inside_dhaka'
            ? $config['inside_dhaka']
            : $config['outside_dhaka'];
    }

    public function zoneLabel(?string $deliveryZone): string
    {
        return match ($deliveryZone) {
            'inside_dhaka' => __('common.inside_dhaka'),
            'outside_dhaka' => __('common.outside_dhaka'),
            default => __('common.free'),
        };
    }

    public function locationFromZone(?string $deliveryZone): array
    {
        if ($deliveryZone === 'inside_dhaka') {
            return [
                'division' => config('bangladesh.dhaka_division', 'Dhaka'),
                'district' => 'Dhaka',
            ];
        }

        return [
            'division' => 'Dhaka',
            'district' => 'Outside Dhaka',
        ];
    }

    public function isInsideDhaka(string $division, ?string $district = null): bool
    {
        if ($division !== config('bangladesh.dhaka_division')) {
            return false;
        }

        if ($district === null) {
            return true;
        }

        return in_array($district, config('bangladesh.dhaka_districts', ['Dhaka']), true);
    }

    public function label(string $division, ?string $district = null): string
    {
        return $this->isInsideDhaka($division, $district) ? 'Inside Dhaka' : 'Outside Dhaka';
    }
}
