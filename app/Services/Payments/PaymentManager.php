<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Enums\PaymentMethod;
use App\Services\PaymentSettingsService;

class PaymentManager
{
    public function __construct(
        private BkashGateway $bkash,
        private NagadGateway $nagad,
        private SslCommerzGateway $sslcommerz,
        private PaymentSettingsService $settings,
    ) {}

    public function gateway(PaymentMethod|string $method): PaymentGatewayInterface
    {
        $key = $method instanceof PaymentMethod ? $method->value : $method;

        return match ($key) {
            'bkash', PaymentMethod::Bkash->value => $this->bkash,
            'nagad', PaymentMethod::Nagad->value => $this->nagad,
            'sslcommerz', PaymentMethod::SslCommerz->value => $this->sslcommerz,
            default => throw new \InvalidArgumentException("Unsupported payment gateway: {$key}"),
        };
    }

    public function gatewayForMethod(PaymentMethod $method): PaymentGatewayInterface
    {
        if (! $method->isOnline()) {
            throw new \InvalidArgumentException('COD does not use a payment gateway.');
        }

        if (! $this->settings->isMethodEnabled($method)) {
            throw new \RuntimeException("{$method->label()} is not enabled.");
        }

        return $this->gateway($method);
    }
}
