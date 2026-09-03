<?php

namespace App\Providers;

use App\Services\CartService;
use App\Support\RedisGuard;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->usePublicPath($this->app->basePath());

        $this->app->booting(function () {
            RedisGuard::configure();
        });
    }

    public function boot(): void
    {
        View::composer('layouts.shop', function ($view) {
            try {
                $view->with('cartCount', app(CartService::class)->count());
            } catch (Throwable) {
                $view->with('cartCount', 0);
            }
        });
    }
}
