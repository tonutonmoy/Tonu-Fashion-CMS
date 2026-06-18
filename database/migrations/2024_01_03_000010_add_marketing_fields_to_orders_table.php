<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_area')->nullable()->after('shipping_upazila');
            $table->string('purchase_event_id')->nullable()->after('order_note');
            $table->string('fbp')->nullable();
            $table->string('fbc')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_area', 'purchase_event_id', 'fbp', 'fbc']);
        });
    }
};
