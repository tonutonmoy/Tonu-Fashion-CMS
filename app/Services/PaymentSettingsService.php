<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class PaymentSettingsService
{
    private const CACHE_KEY = 'payment_settings';

    public function __construct(private SettingRepositoryInterface $settings) {}

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $data = [
                'cod_enabled' => (bool) $this->settings->get('payment', 'cod_enabled', true),
            ];

            foreach (['bkash', 'nagad', 'sslcommerz'] as $gateway) {
                $data[$gateway] = $this->gateway($gateway);
            }

            return $data;
        });
    }

    public function gateway(string $gateway): array
    {
        $sandbox = (bool) $this->settings->get('payment', "{$gateway}_sandbox", true);

        return [
            'enabled' => (bool) $this->settings->get('payment', "{$gateway}_enabled", false),
            'sandbox' => $sandbox,
            'app_key' => $this->settings->get('payment', "{$gateway}_app_key"),
            'app_secret' => $this->settings->get('payment', "{$gateway}_app_secret"),
            'username' => $this->settings->get('payment', "{$gateway}_username"),
            'password' => $this->settings->get('payment', "{$gateway}_password"),
            'merchant_id' => $this->settings->get('payment', "{$gateway}_merchant_id"),
            'store_id' => $this->settings->get('payment', "{$gateway}_store_id"),
            'base_url' => $this->settings->get('payment', "{$gateway}_base_url")
                ?: config("payments.{$gateway}.".($sandbox ? 'sandbox_url' : 'live_url')),
        ];
    }

    public function update(array $data): void
    {
        $flat = ['cod_enabled' => (bool) ($data['cod_enabled'] ?? true)];

        foreach (['bkash', 'nagad', 'sslcommerz'] as $gateway) {
            $flat["{$gateway}_enabled"] = (bool) ($data["{$gateway}_enabled"] ?? false);
            $flat["{$gateway}_sandbox"] = (bool) ($data["{$gateway}_sandbox"] ?? true);
            foreach (['app_key', 'app_secret', 'username', 'password', 'merchant_id', 'store_id', 'base_url'] as $field) {
                if (array_key_exists("{$gateway}_{$field}", $data)) {
                    $flat["{$gateway}_{$field}"] = $data["{$gateway}_{$field}"];
                }
            }
        }

        $this->settings->setMany('payment', $flat);
        Cache::forget(self::CACHE_KEY);
        Cache::forget('app_settings_all');
    }

    public function enabledMethods(): array
    {
        $methods = [];

        if ($this->all()['cod_enabled']) {
            $methods[] = PaymentMethod::CashOnDelivery;
        }

        foreach (['bkash' => PaymentMethod::Bkash, 'nagad' => PaymentMethod::Nagad, 'sslcommerz' => PaymentMethod::SslCommerz] as $key => $method) {
            if ($this->gateway($key)['enabled'] ?? false) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    public function isMethodEnabled(PaymentMethod $method): bool
    {
        return in_array($method, $this->enabledMethods(), true);
    }
}
