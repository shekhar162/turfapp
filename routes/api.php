<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\V1\ProductController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\Admin\V1\ProfileController;
use App\Http\Controllers\OrderController;

Route::get('/v1/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/v1/get-otp', [AuthController::class, 'getOtp']);
Route::post('/v1/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->group(function () {
    // These routes will require Sanctum authentication
    Route::post('/v1/logout', [AuthController::class, 'logout']);

    Route::get('/admin/v1/profile', [ProfileController::class, 'profile']);

    Route::get('/admin/v1/product/list', [ProductController::class, 'index']);
    Route::post('/admin/v1/product/create', [ProductController::class, 'create']);
    Route::post('/admin/v1/product/update', [ProductController::class, 'update']);
    Route::post('/admin/v1/product/details', [ProductController::class, 'show']);
    Route::post('/admin/v1/product/delete', [ProductController::class, 'delete']);

    Route::get('/admin/v1/order/list', [OrderController::class, 'index']);
    Route::post('/admin/v1/order/create', [OrderController::class, 'create']);
    Route::post('/admin/v1/order/update', [OrderController::class, 'update']);
    Route::post('/admin/v1/order/details', [OrderController::class, 'show']);
    Route::post('/admin/v1/order/delete', [OrderController::class, 'delete']);
});
