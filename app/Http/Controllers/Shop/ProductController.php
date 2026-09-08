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

        $categoryCounts = collect(
            ShopCache::rememberJson('shop.category_counts.v1', now()->addMinutes(30), function () {
                return Product::active()
                    ->selectRaw('category_id, COUNT(*) as aggregate')
                    ->groupBy('category_id')
                    ->pluck('aggregate', 'category_id')
                    ->all();
            })
        );

        $brandCountsQuery = Product::active()->whereNotNull('brand')->where('brand', '!=', '');
        if ($activeCategory) {
            $brandCountsQuery->where('category_id', $activeCategory->id);
        }
        $brandCounts = $brandCountsQuery
            ->selectRaw('brand, COUNT(*) as aggregate')
            ->groupBy('brand')
            ->orderBy('brand')
            ->pluck('aggregate', 'brand');

        $brands = $brandCounts->keys();

        $seoImage = null;
        $seoImageAlt = null;
        $firstWithImage = $products->getCollection()->first(fn (Product $product) => filled($product->primary_image));
        if ($firstWithImage) {
            $seoImage = Seo::absolute($firstWithImage->imageUrl());
            $seoImageAlt = $firstWithImage->imageAlt();
        } else {
            $banner = Seo::mainBanner();
            $seoImage = Seo::absolute($banner['src']);
            $seoImageAlt = $activeCategory?->name
                ? $activeCategory->name.' — '.$banner['alt']
                : $banner['alt'];
        }

        $activeFilters = collect([
            request()->filled('search') ? ['key' => 'search', 'label' => 'Search', 'value' => request('search')] : null,
            $activeCategory ? ['key' => 'category', 'label' => 'Category', 'value' => $activeCategory->name] : null,
            request()->filled('brand') ? ['key' => 'brand', 'label' => 'Brand', 'value' => request('brand')] : null,
            request()->filled('sort') && request('sort') !== 'latest'
                ? ['key' => 'sort', 'label' => 'Sort', 'value' => match (request('sort')) {
                    'price_low' => 'Price: Low to High',
                    'price_high' => 'Price: High to Low',
                    'name' => 'Name A–Z',
                    default => request('sort'),
                }] : null,
        ])->filter()->values();

        return view('shop.products.index', compact(
            'products',
            'categories',
            'categoryCounts',
            'brands',
            'brandCounts',
            'activeCategory',
            'activeFilters',
            'seoTitle',
            'seoDescription',
            'seoKeywords',
            'seoImage',
            'seoImageAlt',
        ));
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->with([
                'category:id,name,slug',
                'approvedReviews' => fn ($q) => $q->latest()->take(50),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

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

        $userReview = null;
        if (auth()->check()) {
            $userReview = \App\Models\ProductReview::query()
                ->where('product_id', $product->id)
                ->where(function ($q) {
                    $q->where('user_id', auth()->id())
                        ->orWhere('author_email', strtolower((string) auth()->user()->email));
                })
                ->first();
        }

        return view('shop.products.show', compact('product', 'related', 'userReview'));
    }
}
