<?php

namespace App\Providers;

use App\Helpers\ResponseHelper;
use App\Listeners\ArticleStatusNotification;
use App\Models\Article;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Policies\ArticlePolicy;
use App\Policies\OrderPolicy;
use App\Services\ArticleService;
use App\Services\AuthService;
use App\Services\OrderService;
use App\Services\ReviewService;
use App\Services\UploadService;
use App\Services\UserService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        RateLimiter::for('articles.store', function (Request $request) {
            return env('APP_TEST') ? Limit::none()
                : ($request->user()->isUserVip() ? Limit::perDay(3) : Limit::perDay(1))
                    ->response(function () {
                        return ResponseHelper::error('Too many daily requests for user.', 429);
                    });
        });
        Event::listen([
            ArticleStatusNotification::class
        ]);
        Order::observe(OrderObserver::class);
    }
}
