<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('builder_drafts', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_changes')->default(false);
            $table->json('theme')->nullable();
            $table->json('homepage')->nullable();
            $table->json('hero_slides')->nullable();
            $table->json('footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builder_drafts');
    }
};
