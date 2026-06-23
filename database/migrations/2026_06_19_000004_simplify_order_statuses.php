<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'confirmed' => 'calling_stage',
            'processing' => 'calling_stage',
            'parcel_created' => 'courier',
            'picked' => 'courier',
            'in_transit' => 'courier',
            'shipped' => 'courier',
        ];

        foreach ($map as $from => $to) {
            DB::table('orders')->where('status', $from)->update(['status' => $to]);
        }
    }

    public function down(): void
    {
        // Irreversible — legacy granular statuses are not restored.
    }
};
