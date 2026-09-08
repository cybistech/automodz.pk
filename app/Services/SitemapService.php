<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Support\ShopCache;
use Illuminate\Support\Carbon;

class SitemapService
{
    public function baseUrl(): string
    {
        return rtrim(config('site.url'), '/');
    }

    /**
     * @return list<array{loc: string, lastmod: Carbon, changefreq: string, priority: string}>
     */
    public function pages(): array
    {
        $base = $this->baseUrl();
        $now = now();

        return [
            [
                'loc' => $base.'/',
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => $base.$this->path('products.index'),
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => $base.$this->path('products.index', ['sort' => 'price_low']),
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => '0.7',
            ],
            [
                'loc' => $base.$this->path('sitemap.html'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ],
        ];
    }

    /**
     * @return list<array{loc: string, lastmod: Carbon, changefreq: string, priority: string}>
     */
    public function categories(): array
    {
        $ttl = now()->addMinutes(config('seo.sitemap_cache_minutes', 60));

        return ShopCache::rememberJson('sitemap.categories.v1', $ttl, function () {
            $base = $this->baseUrl();

            return Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['slug', 'updated_at'])
                ->map(fn (Category $category) => [
                    'loc' => $base.$this->path('products.index', ['category' => $category->slug]),
                    'lastmod' => $category->updated_at ?? now(),
                    'changefreq' => 'daily',
                    'priority' => '0.85',
                ])
                ->values()
                ->all();
        });
    }

    /**
     * @return list<array{loc: string, lastmod: Carbon, changefreq: string, priority: string, images: list<array{loc: string, title: string}>}>
     */
    public function products(): array
    {
        $ttl = now()->addMinutes(config('seo.sitemap_cache_minutes', 60));

        return ShopCache::rememberJson('sitemap.products.v1', $ttl, function () {
            $base = $this->baseUrl();

            return Product::query()
                ->active()
                ->orderByDesc('updated_at')
                ->get(['slug', 'name', 'images', 'updated_at'])
                ->map(function (Product $product) use ($base) {
                    $images = [];

                    foreach (array_slice($product->images ?? [], 0, 5) as $index => $path) {
                        $url = $product->imageUrl($path);

                        if (! $url) {
                            continue;
                        }

                        $images[] = [
                            'loc' => $base.$url,
                            'title' => $product->imageAlt($index),
                        ];
                    }

                    return [
                        'loc' => $base.$this->path('products.show', $product->slug),
                        'lastmod' => $product->updated_at ?? now(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                        'images' => $images,
                    ];
                })
                ->values()
                ->all();
        });
    }

    /**
     * @return array{categories: \Illuminate\Support\Collection, products: \Illuminate\Support\Collection}
     */
    public function htmlData(): array
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        $products = Product::query()
            ->active()
            ->with('category:id,name,slug')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category_id']);

        return compact('categories', 'products');
    }

    private function path(string $route, mixed $parameters = []): string
    {
        return route($route, $parameters, false);
    }
}
