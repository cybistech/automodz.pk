<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->usePublicPath($this->app->basePath());
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
