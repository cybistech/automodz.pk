<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('meta_title', trim(View::getSection('title', config('site.name')).' | '.config('site.domain')))</title>
    <meta name="description" content="@yield('meta_description', config('site.description'))">
    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')">
    @else
        <meta name="keywords" content="automodz, auto mods Pakistan, motorcycle parts, bike accessories, car mods, {{ config('site.domain') }}">
    @endif
    <meta property="og:title" content="@yield('meta_title', config('site.name'))">
    <meta property="og:description" content="@yield('meta_description', config('site.description'))">
    <meta property="og:url" content="{{ config('site.url') }}">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ config('site.url') }}{{ request()->getPathInfo() }}">
    <meta name="robots" content="index, follow">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap"></noscript>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <nav class="sticky top-0 z-50 border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" aria-label="{{ config('site.name') }} home">
                <x-brand-logo size="md" :show-tagline="false" />
            </a>

            <form action="{{ route('products.index') }}" class="mx-8 hidden max-w-md flex-1 md:block">
                <div class="relative">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search auto & moto mods, parts, SKU..." class="input-field pl-10">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </form>

            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ route('products.index') }}" class="hidden text-sm font-semibold text-slate-300 transition hover:text-orange-400 sm:block">Shop</a>
                <a href="{{ route('cart.index') }}" class="relative rounded-xl p-2 text-slate-300 transition hover:bg-slate-800 hover:text-orange-400" aria-label="Shopping cart">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @if($cartCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-brand-gradient text-xs font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden text-sm font-semibold text-orange-400 hover:text-orange-300 sm:block">Admin</a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="hidden text-sm font-semibold text-slate-300 hover:text-orange-400 sm:block">Orders</a>
                    <a href="{{ route('profile.edit') }}" class="text-sm font-semibold text-slate-300 hover:text-orange-400">{{ auth()->user()->name }}</a>
                @else
                    <a href="{{ route('orders.track') }}" class="hidden text-sm font-semibold text-slate-300 hover:text-orange-400 sm:block">Track Order</a>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-orange-400">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">{{ session('error') }}</div>
        </div>
    @endif

    <main>@yield('content')</main>

    <footer class="mt-16 border-t border-slate-800 bg-slate-900">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <x-brand-logo size="sm" />
                    <p class="mt-4 text-sm leading-relaxed text-slate-400">{{ config('site.tagline') }}. Your trusted source for auto and motorcycle modifications across Pakistan.</p>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Shop</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('products.index') }}" class="transition hover:text-orange-400">All Parts & Mods</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="transition hover:text-orange-400">Deals & Offers</a></li>
                        <li><a href="{{ route('cart.index') }}" class="transition hover:text-orange-400">Shopping Cart</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Payments</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li>JazzCash</li>
                        <li>Stripe (Card)</li>
                        <li>Bank Transfer</li>
                        <li>Cash on Delivery</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Contact</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="mailto:{{ config('site.email') }}" class="font-medium text-orange-400 transition hover:text-orange-300">{{ config('site.email') }}</a></li>
                        <li><a href="https://{{ config('site.domain') }}" class="transition hover:text-orange-400">{{ config('site.domain') }}</a></li>
                        <li>Karachi, Pakistan</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-800 pt-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ config('site.name') }} — {{ config('site.domain') }}. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
