<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'categories', 'posts', 'cms_pages'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'translations')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->json('translations')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['products', 'categories', 'posts', 'cms_pages'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'translations')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('translations');
                });
            }
        }
    }
};
