<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware(Authenticate::class, 'api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
});
