<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('database.default') === 'mongodb') {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'inventory_settled')) {
                $table->boolean('inventory_settled')->default(false)->after('cogs');
            }
            if (! Schema::hasColumn('orders', 'payment_at')) {
                $table->timestamp('payment_at')->nullable()->after('delivered_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'low_stock_alerts_seen_hash')) {
                $table->string('low_stock_alerts_seen_hash', 64)->nullable()->after('avatar');
            }
        });
    }

    public function down(): void
    {
        if (config('database.default') === 'mongodb') {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'inventory_settled')) {
                $table->dropColumn('inventory_settled');
            }
            if (Schema::hasColumn('orders', 'payment_at')) {
                $table->dropColumn('payment_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'low_stock_alerts_seen_hash')) {
                $table->dropColumn('low_stock_alerts_seen_hash');
            }
        });
    }
};
