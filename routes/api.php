<?php

use App\Enums\UserTypes;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckStockMiddleware;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    Route::apiResource('users', UserController::class)->only([
        'update', 'destroy'
    ]);

    Route::apiResource('articles', ArticleController::class)->only([
        'update', 'destroy'
    ]);

    Route::post('articles', [ArticleController::class, 'store'])->middleware(['throttle:articles.store', CheckUserRole::class.':'.implode(',', UserTypes::businessTypes())]);

    Route::apiResource('reviews', ReviewController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::middleware(CheckStockMiddleware::class)->apiResource('orders', OrderController::class)->only([
        'index', 'store', 'update'
    ]);

    Route::get('attributes/{type}', [AttributeController::class, 'index']);

    Route::apiResource('attributes', AttributeController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::post('uploads', [UploadController::class, 'store']);

    Route::get('redis', [RedisController::class, 'show']);
    Route::post('redis', [RedisController::class, 'store']);

});

Route::get('articles/top-articles', [ArticleController::class, 'getTopArticles']);

Route::apiResource('articles', ArticleController::class)->only([
    'index', 'show'
]);

Route::get('reviews/{articleId}', [ReviewController::class, 'index']);




