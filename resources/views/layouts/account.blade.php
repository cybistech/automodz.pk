<!DOCTYPE html>
<html lang="en-PK">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account') — {{ config('site.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 flex-shrink-0 flex-col border-r border-slate-800 bg-slate-900 lg:flex">
            <div class="p-6">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 overflow-visible">
                    <x-brand-logo size="sm" :show-tagline="false" />
                </a>
                <span class="mt-2 block text-xs font-medium text-slate-500">My Account</span>
            </div>
            <nav class="space-y-1 px-4">
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('profile.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Profile</a>
                <a href="{{ route('orders.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('orders.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">My Orders</a>
                <a href="{{ route('cart.index') }}" class="block rounded-lg px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800">Cart</a>
                <a href="{{ route('home') }}" class="block rounded-lg px-4 py-2.5 text-sm text-slate-400 hover:bg-slate-800">View Store</a>
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-4 py-2.5 text-sm text-orange-400/80 hover:bg-slate-800">Admin Panel</a>
                @endif
            </nav>
            <div class="mt-auto border-t border-slate-800 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-4 py-2.5 text-left text-sm text-slate-400 transition hover:bg-slate-800 hover:text-red-300">
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-slate-800 bg-slate-900/50 px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="lg:hidden">
                            <x-brand-logo size="sm" :show-tagline="false" />
                        </a>
                        <h1 class="text-xl font-semibold">@yield('title', 'Account')</h1>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <nav class="flex items-center gap-1 lg:hidden">
                            <a href="{{ route('profile.edit') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ request()->routeIs('profile.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-400' }}">Profile</a>
                            <a href="{{ route('orders.index') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ request()->routeIs('orders.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-400' }}">Orders</a>
                        </nav>
                        <span class="hidden text-sm text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg px-3 py-1.5 text-sm text-slate-300 transition hover:bg-slate-800 hover:text-red-300">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="mx-4 mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300 sm:mx-6">{{ session('success') }}</div>
            @endif
            @if(session('status') === 'profile-updated')
                <div class="mx-4 mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300 sm:mx-6">Profile updated successfully.</div>
            @endif
            @if(session('status') === 'password-updated')
                <div class="mx-4 mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300 sm:mx-6">Password updated successfully.</div>
            @endif
            @if($errors->any())
                <div class="mx-4 mt-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300 sm:mx-6">
                    <ul class="list-disc space-y-1 pl-5 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <main class="flex-1 p-4 sm:p-6">@yield('content')</main>
        </div>
    </div>
</body>
</html>
