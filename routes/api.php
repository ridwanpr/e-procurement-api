<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\VendorController;
use App\Http\Controllers\Api\V1\ProductController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/vendor', [VendorController::class, 'store']);

        Route::middleware('vendor.check')->group(function () {
            Route::apiResource('/products', ProductController::class);
        });
    });
});
