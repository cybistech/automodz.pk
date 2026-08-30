<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('meta_title', trim(View::getSection('title', config('app.name')).' - Auto Parts Store'))</title>
    <meta name="description" content="@yield('meta_description', 'Shop quality auto and motorcycle parts in Pakistan. Best prices in PKR with JazzCash, Stripe, bank transfer and COD.')">
    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    <meta property="og:title" content="@yield('meta_title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', 'Shop quality auto and motorcycle parts in Pakistan.')">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <nav class="sticky top-0 z-50 border-b border-slate-800 bg-slate-950/90 backdrop-blur-lg">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 font-bold text-white">AP</div>
                <div>
                    <div class="text-lg font-bold tracking-tight">AutoParts<span class="text-orange-500">Pro</span></div>
                    <div class="text-xs text-slate-400">Quality Parts, Fast Delivery</div>
                </div>
            </a>

            <form action="{{ route('products.index') }}" class="hidden flex-1 max-w-md mx-8 md:block">
                <div class="relative">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search parts, SKU, brand..." class="input-field pl-10">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </form>

            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="hidden text-sm font-medium text-slate-300 hover:text-orange-400 sm:block">Shop</a>
                <a href="{{ route('cart.index') }}" class="relative rounded-lg p-2 text-slate-300 hover:bg-slate-800 hover:text-orange-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-orange-500 text-xs font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden text-sm font-medium text-orange-400 hover:text-orange-300 sm:block">Admin</a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="hidden text-sm font-medium text-slate-300 hover:text-orange-400 sm:block">Orders</a>
                    <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-slate-300 hover:text-orange-400">{{ auth()->user()->name }}</a>
                @else
                    <a href="{{ route('orders.track') }}" class="hidden text-sm font-medium text-slate-300 hover:text-orange-400 sm:block">Track Order</a>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-orange-400">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">{{ session('error') }}</div>
        </div>
    @endif

    <main>@yield('content')</main>

    <footer class="mt-16 border-t border-slate-800 bg-slate-900">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <div class="text-lg font-bold">AutoParts<span class="text-orange-500">Pro</span></div>
                    <p class="mt-2 text-sm text-slate-400">Your trusted source for quality auto parts across Pakistan.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white">Shop</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('products.index') }}" class="hover:text-orange-400">All Products</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-orange-400">Shopping Cart</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white">Payments</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li>JazzCash</li>
                        <li>Stripe (Card)</li>
                        <li>Bank Transfer</li>
                        <li>Cash on Delivery</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white">Contact</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li>support@autopartspro.com</li>
                        <li>+92 300 1234567</li>
                        <li>Karachi, Pakistan</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-800 pt-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} AutoPartsPro. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
