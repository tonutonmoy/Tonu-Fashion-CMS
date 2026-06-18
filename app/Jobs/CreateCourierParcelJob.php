<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ParcelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        $parcels->createParcel($order);
    }
}
