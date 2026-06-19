<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use ZipArchive;

class BackupService
{
    private string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
    }

    /** @return list<array{name: string, size: int, created_at: string}> */
    public function list(): array
    {
        File::ensureDirectoryExists($this->backupPath);

        return collect(File::files($this->backupPath))
            ->filter(fn ($file) => Str::endsWith($file->getFilename(), '.zip'))
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
            ])
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }

    public function create(): string
    {
        File::ensureDirectoryExists($this->backupPath);

        $stamp = now()->format('Y-m-d-His');
        $workDir = $this->backupPath.'/work-'.$stamp;
        File::makeDirectory($workDir);

        $sqlFile = $workDir.'/database.sql';
        if (! $this->dumpWithMysqldump($sqlFile)) {
            $this->exportDatabaseFallback($sqlFile);
        }

        $storageZip = $workDir.'/storage.zip';
        $this->zipDirectory(storage_path('app/public'), $storageZip);

        $manifest = [
            'app' => config('app.name'),
            'created_at' => now()->toIso8601String(),
            'database' => config('database.connections.mysql.database'),
            'laravel' => app()->version(),
        ];
        File::put($workDir.'/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        $filename = 'backup-'.$stamp.'.zip';
        $destination = $this->backupPath.'/'.$filename;

        if (! $this->zipDirectory($workDir, $destination)) {
            File::deleteDirectory($workDir);
            throw new \RuntimeException('Could not create backup archive.');
        }

        File::deleteDirectory($workDir);

        return $filename;
    }

    public function downloadPath(string $filename): string
    {
        $path = $this->resolveBackupPath($filename);

        if (! File::exists($path)) {
            throw new \RuntimeException('Backup not found.');
        }

        return $path;
    }

    public function delete(string $filename): void
    {
        $path = $this->resolveBackupPath($filename);

        if (! File::exists($path)) {
            throw new \RuntimeException('Backup not found.');
        }

        File::delete($path);
    }

    public function restore(string $filename): void
    {
        $path = $this->resolveBackupPath($filename);
        $extractDir = $this->backupPath.'/restore-'.Str::uuid();

        File::makeDirectory($extractDir);

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Could not open backup archive.');
        }

        $zip->extractTo($extractDir);
        $zip->close();

        $sqlFile = $extractDir.'/database.sql';
        if (File::exists($sqlFile)) {
            $this->restoreDatabase($sqlFile);
        }

        $storageZip = $extractDir.'/storage.zip';
        if (File::exists($storageZip)) {
            $this->restoreStorage($storageZip);
        }

        File::deleteDirectory($extractDir);

        Artisan::call('optimize:clear');
        Artisan::call('storefront:warm-cache');
    }

    private function resolveBackupPath(string $filename): string
    {
        $filename = basename($filename);

        if (! Str::startsWith($filename, 'backup-') || ! Str::endsWith($filename, '.zip')) {
            throw new \RuntimeException('Invalid backup filename.');
        }

        return $this->backupPath.'/'.$filename;
    }

    private function dumpWithMysqldump(string $destination): bool
    {
        $config = config('database.connections.mysql');

        if (! $this->commandExists('mysqldump')) {
            return false;
        }

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s > %s',
            escapeshellarg((string) $config['host']),
            escapeshellarg((string) $config['port']),
            escapeshellarg((string) $config['username']),
            $config['password'] !== '' ? '--password='.escapeshellarg((string) $config['password']) : '',
            escapeshellarg((string) $config['database']),
            escapeshellarg($destination),
        );

        $result = Process::run($command);

        return $result->successful() && File::exists($destination) && File::size($destination) > 0;
    }

    private function exportDatabaseFallback(string $destination): void
    {
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0])
            ->all();

        $sql = "-- Fashion BD backup\n-- Generated: ".now()->toDateTimeString()."\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $create = DB::selectOne('SHOW CREATE TABLE `'.$table.'`');
            $createSql = array_values((array) $create)[1] ?? '';
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n";

            DB::table($table)->orderBy(DB::raw('1'))->chunk(200, function ($rows) use (&$sql, $table) {
                foreach ($rows as $row) {
                    $values = collect((array) $row)->map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }

                        return "'".addslashes((string) $value)."'";
                    })->implode(', ');

                    $columns = implode('`, `', array_keys((array) $row));
                    $sql .= "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$values});\n";
                }
            });

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($destination, $sql);
    }

    private function restoreDatabase(string $sqlFile): void
    {
        if ($this->commandExists('mysql')) {
            $config = config('database.connections.mysql');
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s %s %s < %s',
                escapeshellarg((string) $config['host']),
                escapeshellarg((string) $config['port']),
                escapeshellarg((string) $config['username']),
                $config['password'] !== '' ? '--password='.escapeshellarg((string) $config['password']) : '',
                escapeshellarg((string) $config['database']),
                escapeshellarg($sqlFile),
            );

            $result = Process::run($command);

            if ($result->successful()) {
                return;
            }
        }

        DB::unprepared(File::get($sqlFile));
    }

    private function restoreStorage(string $zipPath): void
    {
        $target = storage_path('app/public');
        File::ensureDirectoryExists($target);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Could not open storage archive.');
        }

        $zip->extractTo($target);
        $zip->close();
    }

    private function zipDirectory(string $source, string $destination): bool
    {
        if (! extension_loaded('zip')) {
            throw new \RuntimeException('PHP zip extension is required for backups.');
        }

        $zip = new ZipArchive;

        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $source = rtrim($source, DIRECTORY_SEPARATOR);

        if (File::isDirectory($source)) {
            $files = File::allFiles($source);

            foreach ($files as $file) {
                $relative = Str::after($file->getPathname(), $source.DIRECTORY_SEPARATOR);
                $zip->addFile($file->getPathname(), str_replace('\\', '/', $relative));
            }
        } elseif (File::isFile($source)) {
            $zip->addFile($source, basename($source));
        }

        $zip->close();

        return File::exists($destination);
    }

    private function commandExists(string $command): bool
    {
        $check = PHP_OS_FAMILY === 'Windows'
            ? "where {$command}"
            : "command -v {$command}";

        return Process::run($check)->successful();
    }
}
