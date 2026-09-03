<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class ShopCache
{
    public static function rememberJson(string $key, int|\DateInterval|\DateTimeInterface $ttl, Closure $callback): array
    {
        $json = Cache::remember($key, $ttl, function () use ($callback) {
            return json_encode($callback(), JSON_THROW_ON_ERROR);
        });

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    public static function flush(): void
    {
        foreach ([
            'shop.homepage',
            'shop.homepage.v2',
            'shop.categories',
            'shop.categories.v2',
            'shop.brands',
            'shop.brands.v2',
        ] as $key) {
            Cache::forget($key);
        }
    }
}
