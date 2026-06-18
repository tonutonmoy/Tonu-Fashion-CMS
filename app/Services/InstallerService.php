<?php

namespace App\Services;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Models\FooterSetting;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallerService
{
    public function __construct(
        private SettingRepositoryInterface $settings,
        private UserRepositoryInterface $users,
    ) {}

    public function isInstalled(): bool
    {
        if (File::exists($this->installedFilePath())) {
            return true;
        }

        try {
            return (bool) $this->settings->get('app', 'installed', false);
        } catch (\Throwable) {
            return false;
        }
    }

    public function checkRequirements(): array
    {
        $checks = [];

        $checks[] = $this->requirement(
            'PHP Version (>= '.config('installer.min_php_version').')',
            version_compare(PHP_VERSION, config('installer.min_php_version'), '>='),
            PHP_VERSION
        );

        foreach (config('installer.required_extensions', []) as $extension) {
            $checks[] = $this->requirement(
                strtoupper($extension).' Extension',
                extension_loaded($extension)
            );
        }

        $checks[] = $this->requirement(
            'storage/ Writable',
            is_writable(storage_path())
        );

        $checks[] = $this->requirement(
            'bootstrap/cache Writable',
            is_writable(base_path('bootstrap/cache'))
        );

        $envPath = base_path('.env');
        $envWritable = File::exists($envPath)
            ? is_writable($envPath)
            : is_writable(base_path());

        $checks[] = $this->requirement(
            '.env Writable',
            $envWritable,
            File::exists($envPath) ? 'File exists' : 'Will be created from .env.example'
        );

        return $checks;
    }

    public function requirementsPassed(): bool
    {
        return collect($this->checkRequirements())->every(fn ($check) => $check['passed']);
    }

    public function testDatabase(array $config): array
    {
        try {
            $connection = [
                'driver' => 'mysql',
                'host' => $config['db_host'],
                'port' => $config['db_port'] ?? 3306,
                'database' => $config['db_database'],
                'username' => $config['db_username'],
                'password' => $config['db_password'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ];

            config(['database.connections.installer_test' => $connection]);
            DB::purge('installer_test');
            DB::connection('installer_test')->getPdo();

            return ['success' => true, 'message' => 'Database connection successful.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function saveDatabaseConfig(array $config): void
    {
        $this->ensureEnvFile();

        $this->writeEnv([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $config['db_host'],
            'DB_PORT' => (string) ($config['db_port'] ?? 3306),
            'DB_DATABASE' => $config['db_database'],
            'DB_USERNAME' => $config['db_username'],
            'DB_PASSWORD' => $config['db_password'] ?? '',
            'SESSION_DRIVER' => 'file',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'sync',
        ]);

        $this->reloadDatabaseConfig();
        $this->putSession('database', $config);
    }

    public function saveStoreConfig(array $data): void
    {
        $this->putSession('store', $data);
    }

    public function saveAdminConfig(array $data): void
    {
        $this->putSession('admin', $data);
    }

    public function getSessionData(): array
    {
        return session(config('installer.session_key'), []);
    }

    public function createAdmin(array $data): User
    {
        if ($this->users->findByEmail($data['email'])) {
            throw new \RuntimeException('An account with this email already exists.');
        }

        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '01700000000',
            'password' => Hash::make($data['password']),
            'role' => UserRole::SuperAdmin,
            'status' => RecordStatus::Active,
        ]);
    }

    public function runInstallation(): array
    {
        $session = $this->getSessionData();

        if (empty($session['database']) || empty($session['store']) || empty($session['admin'])) {
            throw new \RuntimeException('Installation data is incomplete. Please restart the wizard.');
        }

        $store = $session['store'];
        $admin = $session['admin'];
        $database = $session['database'];

        $this->saveDatabaseConfig($database);

        $log = [];

        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $log[] = 'Application key generated.';
        }

        $this->writeEnv([
            'APP_NAME' => '"'.str_replace('"', '', $store['store_name']).'"',
            'APP_TIMEZONE' => $store['timezone'],
            'APP_URL' => $store['app_url'] ?? config('app.url'),
            'CURRENCY_SYMBOL' => $store['currency_symbol'],
            'CURRENCY_CODE' => $store['currency_code'],
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'database',
            'QUEUE_CONNECTION' => 'database',
        ]);

        Artisan::call('migrate', ['--force' => true]);
        $log[] = 'Database migrations completed.';

        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\InstallSeeder', '--force' => true]);
        $log[] = 'Default data seeded.';

        try {
            if (! File::exists(public_path('storage'))) {
                Artisan::call('storage:link');
                $log[] = 'Storage link created.';
            }
        } catch (\Throwable $e) {
            $log[] = 'Storage link skipped: '.$e->getMessage();
        }

        $this->settings->setMany('store', [
            'name' => $store['store_name'],
            'email' => $store['store_email'],
            'phone' => $store['phone'],
            'address' => $store['address'],
        ]);

        $this->settings->setMany('app', [
            'installed' => true,
            'installed_at' => now()->toIso8601String(),
        ]);

        ThemeSetting::query()->first()?->update([
            'active_theme' => $store['default_theme'],
            'meta_title' => $store['store_name'].' — Premium Fashion Store',
        ]);

        FooterSetting::query()->first()?->update([
            'phone' => $store['phone'],
            'email' => $store['store_email'],
            'address' => $store['address'],
            'copyright_text' => '© '.$store['store_name'].'. All rights reserved.',
        ]);

        $this->createAdmin($admin);
        $log[] = 'Super admin account created.';

        $this->markInstalled();
        $log[] = 'Installation marked complete.';

        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $log[] = 'Configuration, routes, and views cached.';
        } catch (\Throwable $e) {
            $log[] = 'Caching skipped: '.$e->getMessage();
        }

        session()->forget(config('installer.session_key'));

        return $log;
    }

    public function markInstalled(): void
    {
        File::ensureDirectoryExists(storage_path('app'));
        File::put($this->installedFilePath(), now()->toIso8601String());

        try {
            $this->settings->set('app', 'installed', '1', 'boolean');
        } catch (\Throwable) {
            //
        }
    }

    public function availableThemes(): array
    {
        return config('themes.themes', []);
    }

    public function timezones(): array
    {
        return [
            'Asia/Dhaka' => 'Asia/Dhaka (Bangladesh)',
            'UTC' => 'UTC',
        ];
    }

    public function currencies(): array
    {
        return [
            'BDT' => ['symbol' => '৳', 'label' => 'Bangladeshi Taka (৳)'],
        ];
    }

    public function ensureEnvFile(): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            if (! File::exists(base_path('.env.example'))) {
                throw new \RuntimeException('.env.example file is missing.');
            }

            File::copy(base_path('.env.example'), $envPath);

            $this->writeEnv([
                'SESSION_DRIVER' => 'file',
                'CACHE_STORE' => 'file',
                'QUEUE_CONNECTION' => 'sync',
            ]);
        }
    }

    private function writeEnv(array $values): void
    {
        $this->ensureEnvFile();
        $path = base_path('.env');
        $content = File::get($path);

        foreach ($values as $key => $value) {
            $escaped = $this->escapeEnvValue($value);
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$escaped}", $content);
            } else {
                $content .= PHP_EOL."{$key}={$escaped}";
            }
        }

        File::put($path, $content);

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
    }

    private function escapeEnvValue(string $value): string
    {
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return $value;
        }

        if ($value === '' || preg_match('/\s|#/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }

    private function reloadDatabaseConfig(): void
    {
        $this->ensureEnvFile();

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        $dotenv = \Dotenv\Dotenv::createImmutable(base_path());
        $dotenv->load();

        foreach ($_ENV as $key => $value) {
            if (is_string($key) && is_scalar($value)) {
                putenv("{$key}={$value}");
                $_SERVER[$key] = $value;
            }
        }

        config([
            'database.default' => env('DB_CONNECTION', 'mysql'),
            'database.connections.mysql.host' => env('DB_HOST'),
            'database.connections.mysql.port' => env('DB_PORT'),
            'database.connections.mysql.database' => env('DB_DATABASE'),
            'database.connections.mysql.username' => env('DB_USERNAME'),
            'database.connections.mysql.password' => env('DB_PASSWORD'),
        ]);

        DB::purge('mysql');
    }

    private function putSession(string $key, array $data): void
    {
        $sessionKey = config('installer.session_key');
        $all = session($sessionKey, []);
        $all[$key] = $data;
        session([$sessionKey => $all]);
    }

    private function requirement(string $label, bool $passed, ?string $detail = null): array
    {
        return [
            'label' => $label,
            'passed' => $passed,
            'detail' => $detail,
        ];
    }

    private function installedFilePath(): string
    {
        return storage_path('app/installed');
    }
}
