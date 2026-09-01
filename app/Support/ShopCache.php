<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ShopCache
{
    public static function flush(): void
    {
        Cache::forget('shop.homepage');
        Cache::forget('shop.categories');
        Cache::forget('shop.brands');
    }
}
