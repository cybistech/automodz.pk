<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $cached = Cache::remember('shop.homepage', now()->addMinutes(10), function () {
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
        });

        return view('shop.home', [
            'featuredProducts' => Product::hydrate($cached['featuredProducts']),
            'saleProducts' => Product::hydrate($cached['saleProducts']),
            'categories' => Category::hydrate($cached['categories']),
            'newArrivals' => Product::hydrate($cached['newArrivals']),
        ]);
    }

    private function serializeProducts(Collection $products): array
    {
        return $products->map->attributesToArray()->values()->all();
    }
}
