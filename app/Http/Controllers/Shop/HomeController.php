<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\ShopCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class HomeController extends Controller
{
    public function index()
    {
        $cached = ShopCache::rememberJson('shop.homepage.v2', now()->addMinutes(10), fn () => $this->buildHomepagePayload());

        return view('shop.home', [
            'featuredProducts' => $this->hydrateProducts($cached['featuredProducts'] ?? []),
            'saleProducts' => $this->hydrateProducts($cached['saleProducts'] ?? []),
            'categories' => $this->hydrateCategories($cached['categories'] ?? []),
            'newArrivals' => $this->hydrateProducts($cached['newArrivals'] ?? []),
        ]);
    }

    private function buildHomepagePayload(): array
    {
        return [
            'featuredProducts' => $this->serializeProducts(
                Product::active()->featured()->forListing()->latest()->take(8)->get()
            ),
            'saleProducts' => $this->serializeProducts(
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
            'newArrivals' => $this->serializeProducts(
                Product::active()->forListing()->latest()->take(8)->get()
            ),
        ];
    }

    private function serializeProducts(SupportCollection $products): array
    {
        return $products->map->attributesToArray()->values()->all();
    }

    private function hydrateProducts(array $items): Collection
    {
        $rows = array_values(array_filter($items, fn ($item) => is_array($item) && isset($item['id'])));

        return $rows === [] ? new Collection : Product::hydrate($rows);
    }

    private function hydrateCategories(array $items): Collection
    {
        $rows = array_values(array_filter($items, fn ($item) => is_array($item) && isset($item['id'])));

        return $rows === [] ? new Collection : Category::hydrate($rows);
    }
}
