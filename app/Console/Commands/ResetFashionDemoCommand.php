<?php

namespace App\Console\Commands;

use Database\Seeders\SlimFashionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetFashionDemoCommand extends Command
{
    protected $signature = 'fashion:reset-demo {--warm : Warm storefront cache after reset}';

    protected $description = 'Clear catalog data and seed 20 fashion products with hero/category images';

    public function handle(): int
    {
        $this->warn('Removing old catalog data and seeding slim fashion demo…');

        $this->call('db:seed', [
            '--class' => SlimFashionSeeder::class,
            '--force' => true,
        ]);

        if ($this->option('warm')) {
            Artisan::call('storefront:warm-cache', ['--no-interaction' => true]);
            $this->line(Artisan::output());
        }

        $this->info('Fashion demo reset complete: 20 products, 4 categories, 3 hero slides.');

        return self::SUCCESS;
    }
}
