@extends('layouts.shop')

@section('title', 'Home')
@section('meta_title', config('site.name').' | Motorcycle Parts & Mods Pakistan')
@section('meta_description', config('site.description'))

@section('content')
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-orange-950">
    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div>
                <span class="badge bg-orange-500/20 text-orange-400">🏍️ Premium Motorcycle Parts</span>
                <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Mod Your Ride with <span class="text-orange-500">MotoModz</span>
                </h1>
                <p class="mt-6 text-lg text-slate-300">Pakistan's top destination for motorcycle parts, mods, lights, mirrors & accessories. Fast delivery nationwide from <strong class="text-orange-400">{{ config('site.domain') }}</strong></p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="btn-primary text-base">Shop Moto Parts</a>
                    <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="btn-secondary text-base">View Deals</a>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="aspect-square rounded-3xl bg-gradient-to-br from-orange-500/20 to-red-600/20 p-8">
                    <div class="flex h-full flex-col items-center justify-center rounded-2xl border border-orange-500/30 bg-slate-900/80 p-8 text-center">
                        <svg class="h-32 w-32 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="6" cy="17" r="3" stroke-width="1.5"/>
                            <circle cx="18" cy="17" r="3" stroke-width="1.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17h6M6 14l2-5h5l3 5M11 9V6h3l2 3"/>
                        </svg>
                        <p class="mt-4 text-xl font-semibold">Ride Hard. Mod Smart.</p>
                        <p class="mt-2 text-slate-400">Lights • Mirrors • DRL • Holders • More</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold text-white">Shop by Category</h2>
    <p class="mt-1 text-slate-400">Everything your motorcycle needs</p>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="card flex items-center gap-4 p-5 transition hover:border-orange-500/50">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-orange-500/20 text-orange-400 text-2xl">🏍️</div>
                <div>
                    <h3 class="font-semibold text-white">{{ $category->name }}</h3>
                    <p class="text-sm text-slate-400">{{ $category->description }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

<section class="bg-gradient-to-r from-red-950/50 to-orange-950/50 py-16 border-y border-orange-500/20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <span class="badge bg-red-500 text-white">Hot Deals</span>
                <h2 class="mt-2 text-2xl font-bold text-white">Moto Mods on Sale</h2>
                <p class="mt-1 text-slate-400">Limited-time reduced prices in PKR — upgrade your bike for less!</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="text-sm font-medium text-orange-400 hover:text-orange-300">View All Deals →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($saleProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-900/50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-white">Featured Moto Parts</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-orange-400 hover:text-orange-300">View All →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold text-white">New Arrivals</h2>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($newArrivals as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>

<section class="border-t border-slate-800 bg-slate-900 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-4 text-center">
            <div><div class="text-3xl">🚚</div><h3 class="mt-2 font-semibold">Fast Delivery</h3><p class="text-sm text-slate-400">Shipped across Pakistan</p></div>
            <div><div class="text-3xl">🏍️</div><h3 class="mt-2 font-semibold">Moto Specialists</h3><p class="text-sm text-slate-400">Parts for all bikes</p></div>
            <div><div class="text-3xl">💳</div><h3 class="mt-2 font-semibold">Easy Payments</h3><p class="text-sm text-slate-400">JazzCash, Card, COD</p></div>
            <div><div class="text-3xl">🔧</div><h3 class="mt-2 font-semibold">Quality Mods</h3><p class="text-sm text-slate-400">Trusted by riders</p></div>
        </div>
    </div>
</section>
@endsection
