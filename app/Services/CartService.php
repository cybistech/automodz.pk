<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function items(): Collection
    {
        $cart = session(self::SESSION_KEY, []);

        return collect($cart)->map(function (array $item) {
            $product = Product::find($item['product_id']);

            if (! $product || ! $product->is_active) {
                return null;
            }

            $price = $product->effective_price;
            $quantity = min($item['quantity'], max($product->stock, 1));

            return [
                'product_id' => $product->id,
                'product' => $product,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $price * $quantity,
                'image' => $product->primary_image,
            ];
        })->filter()->values();
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = session(self::SESSION_KEY, []);
        $found = false;

        foreach ($cart as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (! $found) {
            $cart[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = session(self::SESSION_KEY, []);

        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        foreach ($cart as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $cart = collect(session(self::SESSION_KEY, []))
            ->reject(fn (array $item) => $item['product_id'] === $productId)
            ->values()
            ->all();

        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('total');
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }
}
