<?php

namespace App\Services;

use App\Contracts\Couriers\CourierGatewayInterface;
use App\Enums\CourierType;
use App\Services\Couriers\PathaoCourierGateway;
use App\Services\Couriers\RedXCourierGateway;
use App\Services\Couriers\SteadfastCourierGateway;

class CourierManager
{
    public function __construct(
        private SteadfastCourierGateway $steadfast,
        private PathaoCourierGateway $pathao,
        private RedXCourierGateway $redx,
        private CourierSettingsService $settings,
    ) {}

    public function gateway(CourierType|string $courier): CourierGatewayInterface
    {
        $type = $courier instanceof CourierType ? $courier : CourierType::from($courier);

        return match ($type) {
            CourierType::Steadfast => $this->steadfast,
            CourierType::Pathao => $this->pathao,
            CourierType::RedX => $this->redx,
        };
    }

    public function defaultGateway(): CourierGatewayInterface
    {
        return $this->gateway($this->settings->defaultCourier());
    }
}
