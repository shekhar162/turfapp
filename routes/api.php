<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::get('/v1/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/v1/get-otp', [AuthController::class, 'getOtp']);
Route::post('/v1/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->group(function () {
    // These routes will require Sanctum authentication
    Route::post('/v1/admin/logout', [AuthController::class, 'logout']);

    Route::post('/v1/admin/profile', [AuthController::class, 'profile']);
});
