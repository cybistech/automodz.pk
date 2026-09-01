<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->forListing()->with('category:id,name,slug');

        if ($request->filled('category')) {
            $categoryId = Category::query()
                ->where('slug', $request->category)
                ->where('is_active', true)
                ->value('id');

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('vehicle_make')) {
            $query->where('vehicle_make', $request->vehicle_make);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_high' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Cache::remember('shop.categories', now()->addHour(), fn () => Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->select(['id', 'name', 'slug'])
            ->get());
        $brands = Cache::remember('shop.brands', now()->addHour(), fn () => Product::active()
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand'));

        return view('shop.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $product = Product::active()->with('category:id,name,slug')->where('slug', $slug)->firstOrFail();

        $viewKey = 'viewed_product_'.$product->id;
        if (! session()->has($viewKey)) {
            $product->increment('views');
            session([$viewKey => true]);
        }

        $related = Product::active()
            ->forListing()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.products.show', compact('product', 'related'))
            ->with('seo', [
                'title' => $product->meta_title ?: $product->name.' | '.config('site.name'),
                'description' => $product->meta_description ?: $product->short_description,
                'keywords' => $product->meta_keywords,
            ]);
    }
}
