<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/concert.php'));
            Route::middleware('web')->group(base_path('routes/discogs.php'));
            Route::middleware('web')->group(base_path('routes/last-fm.php'));
            Route::middleware('web')->group(base_path('routes/music.php'));
            Route::middleware('web')->group(base_path('routes/spotify.php'));
            Route::middleware('web')->group(base_path('routes/tiermaker.php'));
            Route::middleware('web')->group(base_path('routes/wishlist.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
