@extends('layouts.shop')

@section('title', 'Sitemap')
@section('meta_title', 'Sitemap | '.config('site.name'))
@section('meta_description', 'Browse all pages, categories, and products on '.config('site.name').' — '.config('site.domain'))

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 text-sm text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-orange-400">Home</a> /
        <span class="text-slate-300">Sitemap</span>
    </nav>

    <h1 class="text-3xl font-bold text-white">Site Map</h1>
    <p class="mt-2 text-slate-400">All public pages on {{ config('site.domain') }} for visitors and search engines.</p>

    <section class="mt-10">
        <h2 class="text-xl font-semibold text-white">Main Pages</h2>
        <ul class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <li><a href="{{ route('home') }}" class="text-orange-400 hover:text-orange-300">Home</a></li>
            <li><a href="{{ route('products.index') }}" class="text-orange-400 hover:text-orange-300">All Products</a></li>
            <li><a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="text-orange-400 hover:text-orange-300">Deals & Offers</a></li>
            <li><a href="{{ route('orders.track') }}" class="text-orange-400 hover:text-orange-300">Track Order</a></li>
        </ul>
    </section>

    @if($categories->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-xl font-semibold text-white">Categories</h2>
            <ul class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-orange-400 hover:text-orange-300">
                            {{ $category->name }}
                        </a>
                        <span class="text-sm text-slate-500">({{ $category->products_count }})</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($products->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-xl font-semibold text-white">Products</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $products->count() }} active products</p>
            <ul class="mt-4 columns-1 gap-x-8 sm:columns-2 lg:columns-3">
                @foreach($products as $product)
                    <li class="mb-2 break-inside-avoid">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-sm text-orange-400 hover:text-orange-300">
                            {{ $product->name }}
                        </a>
                        @if($product->category)
                            <span class="text-xs text-slate-500"> — {{ $product->category->name }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <p class="mt-12 text-sm text-slate-500">
        Machine-readable sitemap:
        <a href="{{ route('sitemap.index') }}" class="text-orange-400 hover:text-orange-300">sitemap.xml</a>
    </p>
</div>
@endsection
