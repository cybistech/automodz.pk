<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('shop.homepage', now()->addMinutes(10), function () {
            return [
                'featuredProducts' => Product::active()->featured()->forListing()->latest()->take(8)->get(),
                'saleProducts' => Product::active()->onSale()->forListing()->orderByRaw('(price - sale_price) DESC')->take(8)->get(),
                'categories' => Category::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->select(['id', 'name', 'slug', 'description', 'sort_order'])
                    ->take(8)
                    ->get(),
                'newArrivals' => Product::active()->forListing()->latest()->take(8)->get(),
            ];
        });

        return view('shop.home', $data);
    }
}
