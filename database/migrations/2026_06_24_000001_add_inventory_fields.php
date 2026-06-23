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

        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'reserved_stock')) {
                $table->unsignedInteger('reserved_stock')->default(0)->after('stock');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'reserved_stock')) {
                $table->unsignedInteger('reserved_stock')->default(0)->after('stock');
            }
            if (! Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 12, 2)->default(0)->after('sale_price');
            }
        });

        if (! Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->string('product_variant_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->string('order_id')->nullable()->index();
                $table->string('type', 32)->index();
                $table->integer('quantity');
                $table->string('note')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (config('database.default') === 'mongodb') {
            return;
        }

        Schema::dropIfExists('stock_movements');

        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'reserved_stock')) {
                $table->dropColumn('reserved_stock');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('products', 'reserved_stock') ? 'reserved_stock' : null,
                Schema::hasColumn('products', 'purchase_price') ? 'purchase_price' : null,
            ]);
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
