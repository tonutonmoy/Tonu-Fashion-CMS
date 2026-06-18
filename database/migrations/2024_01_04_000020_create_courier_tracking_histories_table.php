<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_tracking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_parcel_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('description')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->index(['courier_parcel_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_tracking_histories');
    }
};
