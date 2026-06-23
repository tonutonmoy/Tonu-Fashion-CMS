<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ParcelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateCourierParcelJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public int $orderId) {}

    public function handle(ParcelService $parcels): void
    {
        $order = Order::query()->with('items')->find($this->orderId);

        if (! $order || $order->hasParcel()) {
            return;
        }

        try {
            $parcels->createParcel($order);
        } catch (\Throwable $e) {
            Log::warning('Auto courier parcel failed (order status was saved)', [
                'order_id' => $this->orderId,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
