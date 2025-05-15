<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::name('admin.')
                ->prefix('api/admin')
                // ->middleware(['auth:sanctum', 'cors', 'IsAdmin'])
                ->middleware(['auth:sanctum', 'IsAdmin'])
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'IsActive' => \App\Http\Middleware\IsActive::class,
            'IsAdmin' => \App\Http\Middleware\IsAdmin::class,
            'Lang' => \App\Http\Middleware\Lang::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
