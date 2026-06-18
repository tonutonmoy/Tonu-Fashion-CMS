<?php

use App\Http\Controllers\Install\InstallerController;
use App\Http\Middleware\EnsureNotInstalled;
use Illuminate\Support\Facades\Route;

Route::prefix('install')
    ->name('install.')
    ->middleware([EnsureNotInstalled::class])
    ->group(function () {
        Route::get('/', [InstallerController::class, 'requirements'])->name('requirements');
        Route::get('/database', [InstallerController::class, 'database'])->name('database');
        Route::post('/database', [InstallerController::class, 'storeDatabase']);
        Route::post('/database/test', [InstallerController::class, 'testDatabase'])->name('database.test');
        Route::get('/store', [InstallerController::class, 'store'])->name('store');
        Route::post('/store', [InstallerController::class, 'saveStore']);
        Route::get('/admin', [InstallerController::class, 'admin'])->name('admin');
        Route::post('/admin', [InstallerController::class, 'saveAdmin']);
        Route::get('/run', [InstallerController::class, 'run'])->name('run');
        Route::post('/run', [InstallerController::class, 'execute'])->name('execute');
    });
