<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InstanceController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\RoadMapController;



Route::prefix('user')->group(function () {
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/get', [UserController::class, 'getUsers']);
    Route::get('/{id}', [UserController::class, 'getOne']);
    Route::post('/create', [UserController::class, 'store']);
    Route::put('/update/{id}', [UserController::class, 'update']);
    Route::delete('/delete/{id}', [UserController::class, 'destroy']);
});


Route::prefix('instance')->group(function () {
    Route::get('/all', [InstanceController::class, 'all']);
    Route::get('/get', [InstanceController::class, 'getInstances']);
    Route::get('/{id}', [InstanceController::class, 'getOne']);
    Route::post('/create', [InstanceController::class, 'store']);
    Route::put('/update/{id}', [InstanceController::class, 'update']);
    Route::delete('/delete/{id}', [InstanceController::class, 'destroy']);
});


Route::prefix('department')->group(function () {
    Route::get('/all', [DepartmentController::class, 'all']);
    Route::get('/{id}', [DepartmentController::class, 'getOne']);
    Route::post('/create', [DepartmentController::class, 'store']);
    Route::put('/update/{id}', [DepartmentController::class, 'update']);
    Route::delete('/delete/{id}', [DepartmentController::class, 'destroy']);
});


Route::prefix('road-map')->group(function () {
    Route::get('/all', [RoadMapController::class, 'all']);
    Route::get('/{id}', [RoadMapController::class, 'getOne']);
    Route::post('/create', [RoadMapController::class, 'store']);
    Route::put('/update/{id}', [RoadMapController::class, 'update']);
    Route::delete('/delete/{id}', [RoadMapController::class, 'destroy']);
});
