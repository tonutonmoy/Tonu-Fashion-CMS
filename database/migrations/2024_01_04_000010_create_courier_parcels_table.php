<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('courier_name');
            $table->string('consignment_id')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('current_status')->default('created');
            $table->timestamp('last_synced_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['courier_name', 'tracking_code']);
            $table->index('current_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_parcels');
    }
};
