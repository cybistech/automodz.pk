<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use App\Services\PaymentGatewayService;
use App\Services\SsoProviderService;
use App\Support\RedisGuard;
use App\Support\ShopCache;
use Illuminate\Support\Facades\Event;
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
        if (class_exists(\SocialiteProviders\Manager\SocialiteWasCalled::class)) {
            Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
                if (class_exists(\SocialiteProviders\Instagram\Provider::class)) {
                    $event->extendSocialite('instagram', \SocialiteProviders\Instagram\Provider::class);
                }
            });
        }

        View::composer(['shop.checkout', 'shop.orders.index', 'shop.orders.show'], function ($view) {
            try {
                $gateways = app(PaymentGatewayService::class);
                $gateways->ensureSeeded();
                $gateways->applyRuntimeConfig();
            } catch (Throwable) {
                //
            }
        });

        View::composer(['auth.login', 'auth.register', 'components.social-login-buttons'], function ($view) {
            try {
                $view->with('ssoProviders', app(SsoProviderService::class)->enabledForLogin());
            } catch (Throwable) {
                $view->with('ssoProviders', collect());
            }
        });

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
