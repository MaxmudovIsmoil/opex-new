<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('success', function (mixed $data = null, int $code = 200) {
            return response()->json([ 
                'success'  => true,
                'data' => $data,
            ], $code);
        });

        Response::macro('fail', function (string|array $error, int $code = 400) {
            return response()->json([
                'success'  => false,
                'error' => $error,
            ], $code);
        });
    }
}
