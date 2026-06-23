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

        if (! Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category', 32)->index();
                $table->decimal('amount', 12, 2);
                $table->date('expense_date')->index();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable()->index();
                $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'cogs')) {
                $table->decimal('cogs', 12, 2)->default(0)->after('total');
            }
        });
    }

    public function down(): void
    {
        if (config('database.default') === 'mongodb') {
            return;
        }

        Schema::dropIfExists('expenses');

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cogs')) {
                $table->dropColumn('cogs');
            }
        });
    }
};
