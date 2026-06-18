<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('active_theme')->default('fashion-modern')->index();
            $table->string('primary_color', 20)->default('#e11d48');
            $table->string('secondary_color', 20)->default('#1f2937');
            $table->string('font_family')->default('Inter');
            $table->string('header_style')->default('default');
            $table->string('footer_style')->default('default');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->json('json_ld_schema')->nullable();
            $table->string('asset_version', 20)->default('1.0.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
