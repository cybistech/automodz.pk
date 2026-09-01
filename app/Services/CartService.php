<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    private ?Collection $resolvedItems = null;

    public function items(): Collection
    {
        if ($this->resolvedItems !== null) {
            return $this->resolvedItems;
        }

        $cart = session(self::SESSION_KEY, []);

        if ($cart === []) {
            return $this->resolvedItems = collect();
        }

        $products = Product::query()
            ->active()
            ->forListing()
            ->whereIn('id', collect($cart)->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        $this->resolvedItems = collect($cart)
            ->map(function (array $item) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
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
            })
            ->filter()
            ->values();

        return $this->resolvedItems;
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
        $this->forgetResolvedItems();
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
        $this->forgetResolvedItems();
    }

    public function remove(int $productId): void
    {
        $cart = collect(session(self::SESSION_KEY, []))
            ->reject(fn (array $item) => $item['product_id'] === $productId)
            ->values()
            ->all();

        session([self::SESSION_KEY => $cart]);
        $this->forgetResolvedItems();
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
        $this->forgetResolvedItems();
    }

    public function count(): int
    {
        return (int) collect(session(self::SESSION_KEY, []))->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('total');
    }

    public function isEmpty(): bool
    {
        return collect(session(self::SESSION_KEY, []))->isEmpty();
    }

    private function forgetResolvedItems(): void
    {
        $this->resolvedItems = null;
    }
}
