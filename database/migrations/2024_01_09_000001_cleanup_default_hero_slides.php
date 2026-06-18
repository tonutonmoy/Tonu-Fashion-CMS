<?php

use App\Models\HeroSlide;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        HeroSlide::query()
            ->whereIn('title', [
                'New Season Collection',
                'Flash Sale — Up to 50% Off',
            ])
            ->delete();
    }

    public function down(): void
    {
        // Seeded slides are not restored on rollback.
    }
};
