<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\ProductCache;
use App\Support\ShopCache;
use Illuminate\Database\Eloquent\Collection;

class HomeController extends Controller
{
    public function index()
    {
        $cached = ShopCache::rememberJson('shop.homepage.v3', now()->addMinutes(10), fn () => $this->buildHomepagePayload());

        return view('shop.home', [
            'featuredProducts' => ProductCache::hydrate($cached['featuredProducts'] ?? []),
            'saleProducts' => ProductCache::hydrate($cached['saleProducts'] ?? []),
            'categories' => $this->hydrateCategories($cached['categories'] ?? []),
            'newArrivals' => ProductCache::hydrate($cached['newArrivals'] ?? []),
        ]);
    }

    private function buildHomepagePayload(): array
    {
        return [
            'featuredProducts' => ProductCache::serialize(
                Product::active()->featured()->forListing()->latest()->take(8)->get()
            ),
            'saleProducts' => ProductCache::serialize(
                Product::active()->onSale()->forListing()->orderByRaw('(price - sale_price) DESC')->take(8)->get()
            ),
            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->select(['id', 'name', 'slug', 'description', 'sort_order'])
                ->take(8)
                ->get()
                ->map->attributesToArray()
                ->values()
                ->all(),
            'newArrivals' => ProductCache::serialize(
                Product::active()->forListing()->latest()->take(8)->get()
            ),
        ];
    }

    private function hydrateCategories(array $items): Collection
    {
        $rows = array_values(array_filter($items, fn ($item) => is_array($item) && isset($item['id'])));

        return $rows === [] ? new Collection : Category::hydrate($rows);
    }
}
