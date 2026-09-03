<?php

namespace App\Support;

class RedisGuard
{
    public static function configure(): void
    {
        if (self::isAvailable()) {
            return;
        }

        if (in_array(config('session.driver'), ['redis', 'cache'], true)) {
            config(['session.driver' => 'file']);
        }

        if (config('cache.default') === 'redis') {
            config(['cache.default' => 'file']);
        }

        if (config('queue.default') === 'redis') {
            config(['queue.default' => 'sync']);
        }
    }

    public static function isAvailable(): bool
    {
        $host = (string) config('database.redis.default.host', '127.0.0.1');
        $port = (int) config('database.redis.default.port', 6379);

        $socket = @fsockopen($host, $port, $errno, $errstr, 1);

        if ($socket === false) {
            return false;
        }

        fclose($socket);

        return true;
    }
}
