<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FetchController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');

    Route::post('/fetch', [FetchController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('fetch.store');

    Route::get('/downloads/{download}', [DownloadController::class, 'show'])
        ->name('downloads.show');

    Route::get('/downloads/{download}/stream', [DownloadController::class, 'stream'])
        ->name('downloads.stream');

    Route::get('/downloads/{download}/file', [DownloadController::class, 'file'])
        ->name('downloads.file');

    Route::post('/downloads/{download}/refetch', [DownloadController::class, 'refetch'])
        ->middleware('throttle:10,1')
        ->name('downloads.refetch');
});
