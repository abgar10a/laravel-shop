<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

// article
Route::apiResource('articles', ArticleController::class);

