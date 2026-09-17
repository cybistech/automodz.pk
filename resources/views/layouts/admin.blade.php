<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('site.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
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
                <a href="{{ route('admin.dashboard') }}" class="inline-flex shrink-0 overflow-visible">
                    <x-brand-logo size="sm" :show-tagline="false" />
                    <span class="mt-1 block text-xs font-medium text-slate-500">Admin Panel</span>
                </a>
            </div>
            <nav class="space-y-1 px-4">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.products.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Products</a>
                <a href="{{ route('admin.reviews.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.reviews.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Reviews</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Categories</a>
                <a href="{{ route('admin.shipping-cities.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.shipping-cities.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Shipping</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-4 py-2.5 text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-300 hover:bg-slate-800' }}">Orders</a>
                <a href="{{ route('home') }}" class="block rounded-lg px-4 py-2.5 text-sm text-slate-400 hover:bg-slate-800">View Store</a>
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

        <div class="flex-1">
            <header class="border-b border-slate-800 bg-slate-900/50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span>
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
                <div class="mx-6 mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="mx-6 mt-4 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-amber-200">{{ session('warning') }}</div>
            @endif
            @if(session('upload_debug'))
                @php($uploadDebug = session('upload_debug'))
                <div class="mx-6 mt-4 rounded-lg border border-blue-500/30 bg-blue-500/10 px-4 py-3 text-blue-100">
                    <p class="font-semibold text-blue-200">Image upload debug</p>
                    <dl class="mt-2 grid gap-1 text-xs sm:grid-cols-2">
                        <div><dt class="text-blue-300/80">Content-Length</dt><dd>{{ number_format($uploadDebug['content_length'] ?? 0) }} bytes</dd></div>
                        <div><dt class="text-blue-300/80">post_max_size</dt><dd>{{ number_format($uploadDebug['post_max_bytes'] ?? 0) }} bytes</dd></div>
                        <div><dt class="text-blue-300/80">upload_max_filesize</dt><dd>{{ number_format($uploadDebug['upload_max_bytes'] ?? 0) }} bytes</dd></div>
                        <div><dt class="text-blue-300/80">Browser reported new files</dt><dd>{{ $uploadDebug['images_attempted'] ?? 0 }}</dd></div>
                        <div><dt class="text-blue-300/80">PHP received files</dt><dd>{{ $uploadDebug['collected_file_count'] ?? 0 }}</dd></div>
                        <div><dt class="text-blue-300/80">Uploaded / kept / removed</dt><dd>{{ ($uploadDebug['uploaded_count'] ?? 0).' / '.($uploadDebug['kept_count'] ?? 0).' / '.($uploadDebug['removed_count'] ?? 0) }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-blue-300/80">products dir writable</dt><dd>{{ ($uploadDebug['disk_checks']['products_dir_writable'] ?? false) ? 'yes' : 'no' }}</dd></div>
                    </dl>
                    @if(! empty($uploadDebug['warnings']))
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-amber-200">
                            @foreach($uploadDebug['warnings'] as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <details class="mt-3">
                        <summary class="cursor-pointer text-xs text-blue-300">Full debug payload</summary>
                        <pre class="mt-2 max-h-64 overflow-auto rounded bg-slate-950/80 p-3 text-[11px] leading-relaxed text-slate-300">{{ json_encode($uploadDebug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </details>
                </div>
            @endif
            @if($errors->any())
                <div class="mx-6 mt-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
                    <ul class="list-disc space-y-1 pl-5 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <main class="p-6">@yield('content')</main>
        </div>
    </div>
</body>
</html>
