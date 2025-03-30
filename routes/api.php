<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    // articles
    Route::get('articles/top-articles', [ArticleController::class, 'getTopArticles']);

    Route::apiResource('articles', ArticleController::class);

    // reviews
    Route::get('reviews/{articleId}', [ReviewController::class, 'index']);

    Route::apiResource('reviews', ReviewController::class)->only([
        'store', 'update', 'destroy'
    ]);

    // orders
    Route::apiResource('orders', OrderController::class);

    // attributes
    Route::get('attributes/{type}', [AttributeController::class, 'index']);

    Route::apiResource('attributes', AttributeController::class)->only([
        'store', 'update', 'destroy'
    ]);

    // uploads
});
    Route::get('uploads/{uploadId}', [UploadController::class, 'show']);


