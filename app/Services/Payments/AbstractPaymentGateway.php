<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\PaymentInitResult;
use App\Data\Payments\PaymentVerifyResult;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\PaymentSettingsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(protected PaymentSettingsService $settings) {}

    abstract public function gateway(): string;

    abstract protected function config(): array;

    public function isConfigured(): bool
    {
        $config = $this->config();

        return ($config['enabled'] ?? false) && ! empty($config['app_key']) && ! empty($config['app_secret']);
    }

    protected function http()
    {
        return Http::timeout(30)->baseUrl(rtrim($this->config()['base_url'], '/'));
    }
}
