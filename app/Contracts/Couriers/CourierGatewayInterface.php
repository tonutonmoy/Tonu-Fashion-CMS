<?php

namespace App\Contracts\Couriers;

use App\Data\Courier\CourierParcelResult;
use App\Data\Courier\CourierStatusResult;
use App\Models\Order;

interface CourierGatewayInterface
{
    public function type(): string;

    public function isConfigured(): bool;

    public function createParcel(Order $order): CourierParcelResult;

    public function fetchStatus(string $consignmentId, ?string $trackingCode = null): CourierStatusResult;

    public function trackingUrl(string $trackingCode): string;
}
