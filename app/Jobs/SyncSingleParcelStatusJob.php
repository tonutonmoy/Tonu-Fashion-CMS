<?php

namespace App\Jobs;

use App\Models\CourierParcel;
use App\Services\ParcelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class SyncSingleParcelStatusJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(public int $parcelId) {}

    public function handle(ParcelService $parcels): void
    {
        $parcel = CourierParcel::query()->with('order')->find($this->parcelId);

        if (! $parcel || ! $parcel->isActive()) {
            return;
        }

        $parcels->syncParcel($parcel);
    }
}
