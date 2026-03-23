<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\API\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
});

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('users', AuthController::class)->names('user');
// });
