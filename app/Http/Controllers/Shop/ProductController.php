<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\Seo;
use App\Support\ShopCache;
use Illuminate\Http\Request;

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

        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = Category::query()
                ->where('slug', $request->category)
                ->where('is_active', true)
                ->first(['id', 'name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords']);
        }

        $seoTitle = $activeCategory?->meta_title
            ?: ($activeCategory
                ? $activeCategory->name.' Parts — Buy Online Pakistan | '.config('site.name')
                : 'Auto & Motorcycle Parts — Buy Online Pakistan | '.config('site.name'));

        $seoDescription = Seo::description(
            $activeCategory?->meta_description ?: $activeCategory?->description,
            'Shop auto and motorcycle mods, lights, mirrors, and performance parts at '.config('site.domain').'. EasyPaisa & COD. Fast delivery from '.config('site.office_city').', Pakistan.'
        );

        $seoKeywords = $activeCategory?->meta_keywords
            ?: 'automodz, auto parts Pakistan, motorcycle parts, bike accessories, '.config('site.domain');

        $categories = Category::hydrate(
            array_values(array_filter(
                ShopCache::rememberJson('shop.categories.v2', now()->addHour(), function () {
                    return Category::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->select(['id', 'name', 'slug'])
                        ->get()
                        ->map->attributesToArray()
                        ->values()
                        ->all();
                }),
                fn ($item) => is_array($item) && isset($item['id'])
            ))
        );
        $brands = collect(
            ShopCache::rememberJson('shop.brands.v2', now()->addHour(), function () {
                return Product::active()
                    ->whereNotNull('brand')
                    ->distinct()
                    ->orderBy('brand')
                    ->pluck('brand')
                    ->values()
                    ->all();
            })
        );

        return view('shop.products.index', compact(
            'products',
            'categories',
            'brands',
            'activeCategory',
            'seoTitle',
            'seoDescription',
            'seoKeywords',
        ));
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
