<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->job(new \App\Jobs\SyncCourierParcelsJob)->everyThirtyMinutes();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureInstalled::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PerformanceProfilerMiddleware::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.permission' => \App\Http\Middleware\CheckAdminPermission::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'not.installed' => \App\Http\Middleware\EnsureNotInstalled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if (! $request->is('admin/*')) {
                return null;
            }

            $maxMb = (int) config('uploads.hero_post_max_mb', 64);
            $perMb = (int) config('uploads.hero_per_file_mb', 16);

            return redirect()->back()->withErrors([
                'media_images' => "Upload too large. Each image max {$perMb}MB, total request max {$maxMb}MB. Compress images or upload fewer at once, then restart the server with: composer serve",
            ]);
        });
    })->create();
