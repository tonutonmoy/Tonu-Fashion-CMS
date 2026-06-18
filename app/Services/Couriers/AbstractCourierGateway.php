<?php

namespace App\Services\Couriers;

use App\Contracts\Couriers\CourierGatewayInterface;
use App\Enums\CourierType;
use App\Models\Order;
use App\Services\CourierSettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractCourierGateway implements CourierGatewayInterface
{
    public function __construct(protected CourierSettingsService $settings) {}

    abstract public function type(): string;

    abstract protected function doCreateParcel(Order $order, array $config): array;

    abstract protected function doFetchStatus(string $consignmentId, ?string $trackingCode, array $config): array;

    public function isConfigured(): bool
    {
        $config = $this->settings->courier($this->type());

        return ($config['enabled'] ?? false)
            && ! empty($config['api_key'])
            && ! empty($config['base_url']);
    }

    public function createParcel(Order $order): \App\Data\Courier\CourierParcelResult
    {
        $config = $this->settings->courier($this->type());

        if (! $this->isConfigured()) {
            return \App\Data\Courier\CourierParcelResult::failure("{$this->type()} courier is not configured.");
        }

        try {
            $result = $this->doCreateParcel($order, $config);

            if (! ($result['success'] ?? false)) {
                return \App\Data\Courier\CourierParcelResult::failure(
                    $result['message'] ?? 'Parcel creation failed.',
                    $result
                );
            }

            $trackingCode = $result['tracking_code'];
            $trackingUrl = $result['tracking_url'] ?? $this->trackingUrl($trackingCode);

            return \App\Data\Courier\CourierParcelResult::success(
                (string) $result['consignment_id'],
                $trackingCode,
                $trackingUrl,
                $result['status'] ?? 'created',
                $result
            );
        } catch (\Throwable $e) {
            Log::error("Courier [{$this->type()}] create failed", ['error' => $e->getMessage(), 'order' => $order->order_number]);

            return \App\Data\Courier\CourierParcelResult::failure($e->getMessage());
        }
    }

    public function fetchStatus(string $consignmentId, ?string $trackingCode = null): \App\Data\Courier\CourierStatusResult
    {
        $config = $this->settings->courier($this->type());

        if (! $this->isConfigured()) {
            return \App\Data\Courier\CourierStatusResult::failure("{$this->type()} courier is not configured.");
        }

        try {
            $result = $this->doFetchStatus($consignmentId, $trackingCode, $config);

            if (! ($result['success'] ?? false)) {
                return \App\Data\Courier\CourierStatusResult::failure(
                    $result['message'] ?? 'Status fetch failed.',
                    $result
                );
            }

            return \App\Data\Courier\CourierStatusResult::success(
                $result['status'] ?? 'unknown',
                $result['description'] ?? null,
                $result['history'] ?? [],
                $result
            );
        } catch (\Throwable $e) {
            Log::error("Courier [{$this->type()}] status sync failed", ['error' => $e->getMessage(), 'consignment' => $consignmentId]);

            return \App\Data\Courier\CourierStatusResult::failure($e->getMessage());
        }
    }

    public function trackingUrl(string $trackingCode): string
    {
        $template = config("couriers.{$this->type()}.tracking_url", '#');

        return str_replace('{tracking_code}', $trackingCode, $template);
    }

    protected function http(array $config)
    {
        return Http::timeout(20)->baseUrl(rtrim($config['base_url'], '/'));
    }

    protected function buildAddress(Order $order): string
    {
        $parts = array_filter([
            $order->shipping_address,
            $order->shipping_area,
            $order->shipping_district,
            $order->shipping_division,
        ]);

        return implode(', ', $parts);
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        return str_starts_with($digits, '88') ? $digits : '88'.$digits;
    }
}
