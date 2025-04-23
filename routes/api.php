<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\OrderActionController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum', 'IsActive'])->group(function () {

//    Route::get('profile', [AuthController::class, 'profile']);
//    Route::post('user/change', [AuthController::class, 'profileUpdate']);


    Route::get('orders/{limit?}', [OrderController::class, 'index'])->where('limit', '[0-9]+');
    Route::get('order/{id}', [OrderController::class, 'getOne']);
    Route::post('order/create', [OrderController::class, 'store']);
    Route::post('order/update', [OrderController::class, 'update']);

    Route::get('order-detail/{id}', [OrderDetailController::class, 'index'])->where('limit', '[0-9]+');
    Route::get('order-action/{id}', [OrderActionController::class, 'index'])->where('limit', '[0-9]+');
    // Route::get('order-road-map/{id}', [OrderRoadMapRunController::class, 'index'])->where('limit', '[0-9]+');
    // Route::get('order-file/{id}', [OrderFileController::class, 'index'])->where('limit', '[0-9]+');
    // Route::post('order-file/create', [OrderFileController::class, 'store']);
    // Route::delete('order-file/delete/{id}', [OrderFileController::class, 'destroy']);


    Route::post('logout', [AuthController::class, 'logout']);

});

