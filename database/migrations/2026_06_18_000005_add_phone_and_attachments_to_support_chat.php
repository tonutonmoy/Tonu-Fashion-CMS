<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_conversations', function (Blueprint $table) {
            $table->string('guest_phone', 20)->nullable()->after('guest_name');
        });

        Schema::table('support_messages', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('body');
        });

        Schema::table('support_messages', function (Blueprint $table) {
            $table->text('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('support_conversations', function (Blueprint $table) {
            $table->dropColumn('guest_phone');
        });

        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropColumn('attachment');
            $table->text('body')->nullable(false)->change();
        });
    }
};
