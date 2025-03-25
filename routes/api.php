<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

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
