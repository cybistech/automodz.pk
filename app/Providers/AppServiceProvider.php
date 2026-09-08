<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use App\Support\RedisGuard;
use App\Support\ShopCache;
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

            try {
                $view->with('footerCategories', Category::hydrate(
                    array_values(array_filter(
                        ShopCache::rememberJson('shop.footer_categories.v1', now()->addHour(), function () {
                            return Category::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->select(['id', 'name', 'slug'])
                                ->take(12)
                                ->get()
                                ->map->attributesToArray()
                                ->values()
                                ->all();
                        }),
                        fn ($item) => is_array($item) && isset($item['id'])
                    ))
                ));
            } catch (Throwable) {
                $view->with('footerCategories', collect());
            }
        });
    }
}
