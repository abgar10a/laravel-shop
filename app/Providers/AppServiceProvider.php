<?php

namespace App\Providers;

use App\Helpers\ResponseHelper;
use App\Listeners\ArticleStatusNotification;
use App\Listeners\OrderToSellerNotification;
use App\Models\Article;
use App\Models\Order;
use App\Models\Review;
use App\Observers\OrderObserver;
use App\Observers\ReviewObserver;
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

    public $singletons = [
        ArticleService::class => ArticleService::class,
        OrderService::class => OrderService::class,
        AuthService::class => AuthService::class,
        ReviewService::class => ReviewService::class,
        UserService::class => UserService::class,
        UploadService::class => UploadService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // policies
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);

        // limiters
        RateLimiter::for('articles.store', function (Request $request) {
            return env('APP_TEST') ? Limit::none()
                : ($request->user()->isUserVip() ? Limit::perDay(3) : Limit::perDay(1))
                    ->response(function () {
                        return ResponseHelper::error('Too many daily requests for user.', 429);
                    });
        });

        // listeners
        Event::listen([
            ArticleStatusNotification::class,
            OrderToSellerNotification::class
        ]);

        // observers
        Order::observe(OrderObserver::class);
        Review::observe(ReviewObserver::class);
    }
}
