<!DOCTYPE html>
<html lang="en-PK">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo-meta />
    <link rel="alternate" type="application/rss+xml" title="{{ config('site.name') }} Products" href="{{ route('feed.products') }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap.index') }}">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="image_src" href="{{ \App\Support\Seo::defaultOgImage() }}">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preload" href="/images/logo.svg" as="image" type="image/svg+xml">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap"></noscript>
    @vite(['resources/css/app.css'])
    @stack('head')
    @stack('jsonld')
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <nav class="sticky top-0 z-50 border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 overflow-visible py-0.5" aria-label="{{ config('site.name') }} home">
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
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <x-brand-logo size="sm" />
                    <p class="mt-4 text-sm leading-relaxed text-slate-400">{{ config('site.tagline') }}. Your trusted source for auto and motorcycle modifications across Pakistan.</p>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Shop</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('products.index') }}" class="transition hover:text-orange-400">All Parts & Mods</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="transition hover:text-orange-400">Deals & Offers</a></li>
                        <li><a href="{{ route('sitemap.html') }}" class="transition hover:text-orange-400">Sitemap</a></li>
                        <li><a href="{{ route('cart.index') }}" class="transition hover:text-orange-400">Shopping Cart</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Categories</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        @forelse($footerCategories ?? [] as $category)
                            <li>
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="transition hover:text-orange-400">{{ $category->name }}</a>
                            </li>
                        @empty
                            <li><a href="{{ route('products.index') }}" class="transition hover:text-orange-400">All Products</a></li>
                        @endforelse
                    </ul>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Payments</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li>EasyPaisa</li>
                        <li>Cash on Delivery</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display text-lg font-bold text-white">Contact</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="mailto:{{ config('site.email') }}" class="font-medium text-orange-400 transition hover:text-orange-300">{{ config('site.email') }}</a></li>
                        <li>
                            <a href="https://wa.me/{{ config('site.whatsapp') }}?text={{ rawurlencode(config('site.whatsapp_message')) }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-[#25D366]">
                                WhatsApp {{ config('site.whatsapp_display') }}
                            </a>
                        </li>
                        <li><a href="https://{{ config('site.domain') }}" class="transition hover:text-orange-400">{{ config('site.domain') }}</a></li>
                        <li>{{ config('site.office_city') }}, {{ config('site.office_country') }}</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-800 pt-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ config('site.name') }} — {{ config('site.domain') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <x-whatsapp-float />
    @stack('scripts')
</body>
</html>
