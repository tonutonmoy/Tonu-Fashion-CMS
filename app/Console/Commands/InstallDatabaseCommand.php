<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallDatabaseCommand extends Command
{
    protected $signature = 'app:install-database {--fresh : Drop all tables before migrating}';

    protected $description = 'Run MySQL migrations and seed the Fashion BD dataset';

    public function handle(): int
    {
        if (config('database.default') !== 'mysql') {
            $this->error('Set DB_CONNECTION=mysql in .env first.');

            return self::FAILURE;
        }

        try {
            DB::connection()->getPdo();
            $this->info('MySQL connection OK.');
        } catch (\Throwable $e) {
            $this->error('MySQL connection failed: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Running migrate:fresh…');
            Artisan::call('migrate:fresh', ['--force' => true]);
        } else {
            Artisan::call('migrate', ['--force' => true]);
        }

        $this->line(Artisan::output());

        $this->info('Seeding website data…');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder', '--force' => true]);
        $this->line(Artisan::output());

        file_put_contents(storage_path('app/installed'), now()->toIso8601String());

        try {
            if (! file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
                $this->info('Storage link created.');
            }
        } catch (\Throwable $e) {
            $this->warn('Storage link skipped: '.$e->getMessage());
        }

        Artisan::call('optimize:clear');

        try {
            Artisan::call('demo:fix-images');
        } catch (\Throwable) {
            //
        }

        Artisan::call('storefront:warm-cache');

        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
        } catch (\Throwable) {
            //
        }

        $this->info('MySQL install complete.');

        return self::SUCCESS;
    }
}
