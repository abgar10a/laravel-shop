<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->block(2, 2);

    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::post('/confirm-code', [AuthController::class, 'confirmCode']);

    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::get('/{provider}', [AuthController::class, 'redirectToProvider']);

    Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
});


Route::middleware(Authenticate::class)->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});
