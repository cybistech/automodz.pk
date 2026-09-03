<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResilientCache
{
    public static function remember(string $key, int|\DateInterval|\DateTimeInterface $ttl, Closure $callback): mixed
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (Throwable $e) {
            Log::warning('Cache remember failed, using fallback', ['key' => $key, 'error' => $e->getMessage()]);

            return $callback();
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::get($key, $default);
        } catch (Throwable $e) {
            Log::warning('Cache get failed', ['key' => $key, 'error' => $e->getMessage()]);

            return $default;
        }
    }

    public static function put(string $key, mixed $value, int|\DateInterval|\DateTimeInterface|null $ttl = null): bool
    {
        try {
            return Cache::put($key, $value, $ttl);
        } catch (Throwable $e) {
            Log::warning('Cache put failed', ['key' => $key, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public static function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (Throwable $e) {
            Log::warning('Cache forget failed', ['key' => $key, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
