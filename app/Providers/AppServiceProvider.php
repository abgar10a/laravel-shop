<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Order;
use App\Models\Review;
use App\Policies\ArticlePolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use App\Services\ArticleService;
use App\Services\AuthService;
use App\Services\OrderService;
use App\Services\ReviewService;
use App\Services\UploadService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ArticleService::class, function ($app) {
            return new ArticleService();
        });
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });
        $this->app->singleton(ReviewService::class, function ($app) {
            return new ReviewService();
        });
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService();
        });
        $this->app->singleton(ArticleService::class, function ($app) {
            return new ArticleService();
        });
        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
