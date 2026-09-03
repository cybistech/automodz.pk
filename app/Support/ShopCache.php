<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShopCache
{
    public static function rememberJson(string $key, int|\DateInterval|\DateTimeInterface $ttl, Closure $callback): array
    {
        try {
            $json = Cache::remember($key, $ttl, function () use ($callback) {
                return json_encode($callback(), JSON_THROW_ON_ERROR);
            });

            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($decoded)) {
                return self::fresh($callback, $key, 'invalid cache payload');
            }

            return $decoded;
        } catch (Throwable $e) {
            return self::fresh($callback, $key, $e->getMessage());
        }
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
            try {
                Cache::forget($key);
            } catch (Throwable $e) {
                Log::warning('ShopCache flush failed', ['key' => $key, 'error' => $e->getMessage()]);
            }
        }
    }

    private static function fresh(Closure $callback, string $key, string $reason): array
    {
        Log::warning('ShopCache fallback to database', ['key' => $key, 'reason' => $reason]);

        $data = $callback();

        return is_array($data) ? $data : [];
    }
}
