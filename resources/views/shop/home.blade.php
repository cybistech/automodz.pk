@extends('layouts.shop')

@section('title', 'Home')
@section('meta_title', config('site.name').' | Premium Auto & Motorcycle Mods Pakistan')
@section('meta_description', config('site.description'))
@section('meta_keywords', 'automodz, auto mods Pakistan, motorcycle parts, bike accessories, car mods, performance parts, automodz.pk')

@section('content')
<section class="relative overflow-hidden bg-slate-950 hero-grid">
    <div class="pointer-events-none absolute inset-0 bg-hero-glow"></div>
    <div class="pointer-events-none absolute -right-24 top-0 h-96 w-96 rounded-full bg-orange-500/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-red-600/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div>
                <span class="badge border border-orange-500/30 bg-orange-500/10 text-orange-300">
                    Pakistan's Premium Mod Destination
                </span>
                <h1 class="mt-5 font-display text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Upgrade Your Ride with
                    <span class="brand-text">AutoModz</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">
                    From motorcycle DRLs and mirrors to performance auto mods — {{ config('site.name') }} delivers quality parts across Pakistan with fast shipping and trusted support at
                    <strong class="text-orange-400">{{ config('site.domain') }}</strong>.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="btn-primary text-base">Shop All Mods</a>
                    <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="btn-secondary text-base">Explore Deals</a>
                </div>
                <dl class="mt-10 grid grid-cols-3 gap-4 border-t border-slate-800 pt-8">
                    <div>
                        <dt class="font-display text-2xl font-bold text-white">500+</dt>
                        <dd class="text-sm text-slate-400">Quality Parts</dd>
                    </div>
                    <div>
                        <dt class="font-display text-2xl font-bold text-white">24h</dt>
                        <dd class="text-sm text-slate-400">Fast Dispatch</dd>
                    </div>
                    <div>
                        <dt class="font-display text-2xl font-bold text-white">PK</dt>
                        <dd class="text-sm text-slate-400">Nationwide</dd>
                    </div>
                </dl>
            </div>

            <div class="relative">
                <div class="relative overflow-hidden rounded-3xl border border-orange-500/20 bg-gradient-to-br from-slate-900 via-slate-950 to-orange-950/40 p-8 shadow-brand">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(251,191,36,0.12),transparent_55%)]"></div>
                    <div class="relative flex flex-col items-center overflow-visible text-center">
                        <x-brand-logo size="xl" :show-tagline="false" class="items-center" />
                        <p class="mt-6 font-display text-2xl font-bold text-white">Built for Riders & Drivers</p>
                        <p class="mt-2 text-slate-400">Lights · Mirrors · DRL · Holders · Performance</p>
                        <div class="mt-8 grid w-full grid-cols-2 gap-3 text-left">
                            <div class="rounded-xl border border-slate-700/60 bg-slate-900/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-orange-400">Moto Mods</p>
                                <p class="mt-1 text-sm text-slate-300">Bike lights, indicators & accessories</p>
                            </div>
                            <div class="rounded-xl border border-slate-700/60 bg-slate-900/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-orange-400">Auto Parts</p>
                                <p class="mt-1 text-sm text-slate-300">Upgrades for every vehicle type</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="border-y border-slate-800 bg-slate-900/50 py-5">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-10 gap-y-3 px-4 text-sm text-slate-400 sm:px-6 lg:px-8">
        <span class="flex items-center gap-2"><span class="text-orange-400">✓</span> Genuine Quality Parts</span>
        <span class="flex items-center gap-2"><span class="text-orange-400">✓</span> JazzCash & COD</span>
        <span class="flex items-center gap-2"><span class="text-orange-400">✓</span> Guest Checkout</span>
        <span class="flex items-center gap-2"><span class="text-orange-400">✓</span> {{ config('site.email') }}</span>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-400">Categories</p>
            <h2 class="section-title mt-2">Shop by Mod Type</h2>
            <p class="mt-2 text-slate-400">Curated collections for every upgrade path</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-orange-400 hover:text-orange-300">View all →</a>
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="card-hover group flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-brand-gradient text-lg font-bold text-white shadow-brand">
                    {{ strtoupper(substr($category->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-white group-hover:text-orange-300">{{ $category->name }}</h3>
                    <p class="text-sm text-slate-400">{{ $category->description }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

<section class="border-y border-orange-500/20 bg-gradient-to-r from-red-950/40 via-slate-950 to-orange-950/40 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="badge bg-red-500 text-white">Hot Deals</span>
                <h2 class="section-title mt-3">Mods on Sale</h2>
                <p class="mt-2 text-slate-400">Limited-time PKR discounts — upgrade for less</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="text-sm font-semibold text-orange-400 hover:text-orange-300">All deals →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($saleProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-900/40 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 class="section-title">Featured Upgrades</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-orange-400 hover:text-orange-300">View all →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h2 class="section-title">New Arrivals</h2>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-orange-400 hover:text-orange-300">Fresh drops →</a>
    </div>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($newArrivals as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>

<section class="border-t border-slate-800 bg-slate-900 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-4">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-500/15 text-2xl">🚚</div>
                <h3 class="mt-4 font-display text-lg font-bold text-white">Fast Delivery</h3>
                <p class="mt-2 text-sm text-slate-400">Shipped across Pakistan from Karachi</p>
            </div>
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-500/15 text-2xl">⚡</div>
                <h3 class="mt-4 font-display text-lg font-bold text-white">Mod Specialists</h3>
                <p class="mt-2 text-sm text-slate-400">Auto & motorcycle experts</p>
            </div>
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-500/15 text-2xl">💳</div>
                <h3 class="mt-4 font-display text-lg font-bold text-white">Easy Payments</h3>
                <p class="mt-2 text-sm text-slate-400">JazzCash, card, bank & COD</p>
            </div>
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-500/15 text-2xl">🛡️</div>
                <h3 class="mt-4 font-display text-lg font-bold text-white">Trusted Quality</h3>
                <p class="mt-2 text-sm text-slate-400">Loved by riders nationwide</p>
            </div>
        </div>
    </div>
</section>

<section class="relative overflow-hidden border-t border-slate-800 py-16">
    <div class="pointer-events-none absolute inset-0 bg-brand-gradient opacity-10"></div>
    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Ready to mod your ride?</h2>
        <p class="mx-auto mt-4 max-w-2xl text-slate-300">Browse hundreds of premium parts or reach our team at <a href="mailto:{{ config('site.email') }}" class="font-semibold text-orange-400 hover:text-orange-300">{{ config('site.email') }}</a></p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ route('products.index') }}" class="btn-primary">Start Shopping</a>
            <a href="mailto:{{ config('site.email') }}" class="btn-secondary">Contact Us</a>
        </div>
    </div>
</section>
@endsection
