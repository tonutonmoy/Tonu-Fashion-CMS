<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->decimal('title_size', 4, 2)->nullable()->after('content_layout');
            $table->decimal('subtitle_size', 4, 2)->nullable()->after('title_size');
            $table->decimal('button_size', 4, 2)->nullable()->after('subtitle_size');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['title_size', 'subtitle_size', 'button_size']);
        });
    }
};
