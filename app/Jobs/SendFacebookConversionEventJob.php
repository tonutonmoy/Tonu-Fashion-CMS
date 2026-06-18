<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Services\FacebookConversionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendFacebookConversionEventJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $eventName,
        public array $userContext,
        public array $customData,
        public ?int $productId = null,
        public ?int $orderId = null
    ) {}

    public function handle(FacebookConversionApiService $capi): void
    {
        if ($this->orderId) {
            $order = Order::with('items')->find($this->orderId);
            if ($order) {
                $capi->purchase($order, $this->userContext);

                return;
            }
        }

        $capi->sendEvent(
            $this->eventName,
            $this->userContext,
            $this->customData,
            $this->userContext['event_id'] ?? null
        );
    }
}
