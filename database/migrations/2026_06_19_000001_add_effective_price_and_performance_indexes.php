<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'effective_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('effective_price', 12, 2)->default(0)->after('sale_price');
                $table->index('effective_price');
                $table->index('created_at');
            });

            DB::table('products')->update([
                'effective_price' => DB::raw('COALESCE(sale_price, regular_price)'),
            ]);
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'order_number')) {
                    $table->index('order_number');
                }
                if (Schema::hasColumn('orders', 'customer_phone')) {
                    $table->index('customer_phone');
                }
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'effective_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('effective_price');
            });
        }
    }
};
