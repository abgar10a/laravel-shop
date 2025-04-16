<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    Route::apiResource('users', UserController::class)->only([
        'update', 'destroy'
    ]);

    Route::apiResource('articles', ArticleController::class)->only([
        'update', 'destroy'
    ]);

    Route::post('articles', [ArticleController::class, 'store'])->middleware('throttle:articles.store');

    Route::apiResource('reviews', ReviewController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::apiResource('orders', OrderController::class)->only([
        'index', 'store', 'update'
    ]);

    Route::get('attributes/{type}', [AttributeController::class, 'index']);

    Route::apiResource('attributes', AttributeController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::post('uploads', [UploadController::class, 'store']);
});

Route::get('articles/top-articles', [ArticleController::class, 'getTopArticles']);

Route::apiResource('articles', ArticleController::class)->only([
    'index', 'show'
]);

Route::get('reviews/{articleId}', [ReviewController::class, 'index']);

