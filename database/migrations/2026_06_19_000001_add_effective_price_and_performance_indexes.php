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
            });

            DB::table('products')->update([
                'effective_price' => DB::raw('COALESCE(sale_price, regular_price)'),
            ]);
        }

        if (Schema::hasTable('products')) {
            $this->addIndexIfMissing('products', 'effective_price');
            $this->addIndexIfMissing('products', 'created_at');
        }

        if (Schema::hasTable('orders')) {
            $this->addIndexIfMissing('orders', 'order_number');
            if (Schema::hasColumn('orders', 'customer_phone')) {
                $this->addIndexIfMissing('orders', 'customer_phone');
            }
            $this->addIndexIfMissing('orders', 'created_at');
        }

        if (Schema::hasTable('categories')) {
            $this->addIndexIfMissing('categories', 'slug');
        }

        if (Schema::hasTable('brands')) {
            $this->addIndexIfMissing('brands', 'slug');
        }

        if (Schema::hasTable('posts')) {
            $this->addIndexIfMissing('posts', 'slug');
            $this->addIndexIfMissing('posts', 'created_at');
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

    private function addIndexIfMissing(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_index";

        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? '') === $indexName) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $table) use ($column) {
            $table->index($column);
        });
    }
};
