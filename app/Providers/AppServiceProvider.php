<?php

namespace App\Providers;

use App\Services\ArticleService;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Services\OrderService;
use App\Services\ReviewService;
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
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
