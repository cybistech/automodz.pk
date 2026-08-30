<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->featured()->with('category')->latest()->take(8)->get();
        $saleProducts = Product::active()->onSale()->with('category')->orderByRaw('(price - sale_price) DESC')->take(8)->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->take(8)->get();
        $newArrivals = Product::active()->with('category')->latest()->take(8)->get();

        return view('shop.home', compact('featuredProducts', 'saleProducts', 'categories', 'newArrivals'));
    }
}
