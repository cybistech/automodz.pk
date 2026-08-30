@extends('layouts.shop')

@section('title', 'Home')

@section('content')
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-orange-950">
    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div>
                <span class="badge bg-orange-500/20 text-orange-400">Premium Auto Parts</span>
                <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Find the Right Part for <span class="text-orange-500">Your Vehicle</span>
                </h1>
                <p class="mt-6 text-lg text-slate-300">Shop thousands of quality auto parts with fast delivery across Pakistan. Pay with JazzCash, Stripe, bank transfer, or cash on delivery.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="btn-primary text-base">Shop Now</a>
                    <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="btn-secondary text-base">New Arrivals</a>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="aspect-square rounded-3xl bg-gradient-to-br from-orange-500/20 to-red-600/20 p-8">
                    <div class="flex h-full flex-col items-center justify-center rounded-2xl border border-orange-500/30 bg-slate-900/80 p-8 text-center">
                        <svg class="h-32 w-32 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        <p class="mt-4 text-xl font-semibold">Genuine Parts Guaranteed</p>
                        <p class="mt-2 text-slate-400">Engine • Brakes • Electrical • Body</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold text-white">Shop by Category</h2>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="card flex items-center gap-4 p-5 transition hover:border-orange-500/50">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-orange-500/20 text-orange-400">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
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
                <h2 class="mt-2 text-2xl font-bold text-white">Special Offers & Sales</h2>
                <p class="mt-1 text-slate-400">Limited-time reduced prices in PKR — grab them before they're gone!</p>
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
            <h2 class="text-2xl font-bold text-white">Featured Products</h2>
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
            <div><div class="text-3xl">🚚</div><h3 class="mt-2 font-semibold">Fast Delivery</h3><p class="text-sm text-slate-400">Nationwide shipping</p></div>
            <div><div class="text-3xl">✅</div><h3 class="mt-2 font-semibold">Genuine Parts</h3><p class="text-sm text-slate-400">Quality guaranteed</p></div>
            <div><div class="text-3xl">💳</div><h3 class="mt-2 font-semibold">Easy Payments</h3><p class="text-sm text-slate-400">Multiple options</p></div>
            <div><div class="text-3xl">🔧</div><h3 class="mt-2 font-semibold">Expert Support</h3><p class="text-sm text-slate-400">Help finding parts</p></div>
        </div>
    </div>
</section>
@endsection
