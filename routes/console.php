<?php

use App\Jobs\SyncCourierParcelsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('courier:sync', function () {
    dispatch_sync(new SyncCourierParcelsJob);
    $this->info('Courier parcel statuses synced.');
})->purpose('Sync all active courier parcel statuses');
