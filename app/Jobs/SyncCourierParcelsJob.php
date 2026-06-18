<?php

namespace App\Jobs;

use App\Services\ParcelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncCourierParcelsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function handle(ParcelService $parcels): void
    {
        $parcels->syncActiveParcels();
    }
}
