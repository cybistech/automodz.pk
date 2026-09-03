<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductCache
{
    private const JSON_ATTRIBUTES = ['images', 'specifications'];

    public static function serialize(Collection $products): array
    {
        return $products->map(function (Product $product) {
            $attributes = $product->getAttributes();

            foreach (self::JSON_ATTRIBUTES as $key) {
                if (array_key_exists($key, $attributes) && is_array($attributes[$key])) {
                    $attributes[$key] = json_encode($attributes[$key], JSON_THROW_ON_ERROR);
                }
            }

            return $attributes;
        })->values()->all();
    }

    public static function hydrate(array $items): Collection
    {
        $rows = array_values(array_filter(array_map(function ($item) {
            if (! is_array($item) || ! isset($item['id'])) {
                return null;
            }

            foreach (self::JSON_ATTRIBUTES as $key) {
                if (array_key_exists($key, $item) && is_array($item[$key])) {
                    $item[$key] = json_encode($item[$key], JSON_THROW_ON_ERROR);
                }
            }

            return $item;
        }, $items)));

        return $rows === [] ? new Collection : Product::hydrate($rows);
    }
}
