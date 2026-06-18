<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MongoInstallCommand extends Command
{
    protected $signature = 'mongo:install {--fresh : Drop all collections before seeding}';

    protected $description = 'Prepare MongoDB Atlas and seed the full Fashion BD dataset';

    public function handle(): int
    {
        if (config('database.default') !== 'mongodb') {
            $this->error('Set DB_CONNECTION=mongodb in .env first.');

            return self::FAILURE;
        }

        try {
            DB::connection('mongodb')->getMongoDB()->command(['ping' => 1]);
            $this->info('MongoDB connection OK.');
        } catch (\Throwable $e) {
            $this->error('MongoDB connection failed: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Dropping collections…');
            foreach (DB::connection('mongodb')->getMongoDB()->listCollections() as $collection) {
                DB::connection('mongodb')->getMongoDB()->dropCollection($collection->getName());
            }
            $this->info('Collections dropped.');
        }

        $this->info('Seeding full website data…');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder', '--force' => true]);
        $this->line(Artisan::output());

        file_put_contents(storage_path('app/installed'), now()->toIso8601String());

        Artisan::call('optimize:clear');
        Artisan::call('mongo:create-indexes');
        Artisan::call('demo:fix-images');
        Artisan::call('storefront:warm-cache');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        $this->info('MongoDB install complete.');

        return self::SUCCESS;
    }
}
