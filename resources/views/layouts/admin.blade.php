<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - AutoPartsPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 flex-shrink-0 border-r border-slate-800 bg-slate-900 lg:block">
            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">AutoParts<span class="text-orange-500">Admin</span></a>
            </div>
            <nav class="space-y-1 px-4">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.products.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Orders</a>
                <a href="{{ route('home') }}" class="block rounded-lg px-4 py-2.5 text-sm text-slate-400 hover:bg-slate-800">View Store</a>
            </nav>
        </aside>

        <div class="flex-1">
            <header class="border-b border-slate-800 bg-slate-900/50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold">@yield('title', 'Dashboard')</h1>
                    <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span>
                </div>
            </header>

            @if(session('success'))
                <div class="mx-6 mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300">{{ session('success') }}</div>
            @endif

            <main class="p-6">@yield('content')</main>
        </div>
    </div>
</body>
</html>
