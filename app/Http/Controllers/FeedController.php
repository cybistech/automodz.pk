<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Seo;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function products(): Response
    {
        $limit = max(10, min(100, (int) config('seo.feed_items', 50)));

        $products = Product::query()
            ->active()
            ->with('category:id,name,slug')
            ->latest('updated_at')
            ->take($limit)
            ->get(['id', 'name', 'slug', 'short_description', 'meta_description', 'updated_at', 'category_id']);

        return response()
            ->view('feeds.products', [
                'siteUrl' => Seo::siteUrl(),
                'siteName' => config('site.name'),
                'products' => $products,
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=1800');
    }
}
