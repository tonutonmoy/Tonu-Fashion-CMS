<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderSmsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(
        public int $orderId,
        public string $type
    ) {}

    public function handle(SmsService $sms): void
    {
        $order = Order::query()->with('courierParcel')->find($this->orderId);
        if (! $order) {
            return;
        }

        $result = match ($this->type) {
            'confirmed' => $sms->sendOrderConfirmed($order),
            'shipped' => $sms->sendOrderShipped($order),
            'delivered' => $sms->sendOrderDelivered($order),
            'parcel_created' => $sms->sendParcelCreated($order),
            'returned' => $sms->sendOrderReturned($order),
            default => null,
        };

        if ($result && ! $result->success) {
            throw new \RuntimeException($result->message);
        }
    }
}
