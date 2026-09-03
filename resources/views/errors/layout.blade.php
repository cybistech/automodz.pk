<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('site.name', 'AutoModz') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="mx-auto flex min-h-screen max-w-2xl flex-col items-center justify-center px-4 py-16 text-center">
        <a href="{{ url('/') }}" class="mb-8">
            <x-brand-logo size="lg" :show-tagline="false" />
        </a>
        <p class="font-display text-6xl font-bold text-orange-500">@yield('code')</p>
        <h1 class="mt-4 font-display text-2xl font-bold text-white">@yield('heading')</h1>
        <p class="mt-3 text-slate-400">@yield('message')</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ url('/') }}" class="btn-primary">Back to Home</a>
            <a href="{{ url('/products') }}" class="btn-secondary">Browse Parts</a>
        </div>
        <p class="mt-10 text-sm text-slate-500">
            Need help? <a href="mailto:{{ config('site.email', 'info@automodz.pk') }}" class="text-orange-400 hover:text-orange-300">{{ config('site.email', 'info@automodz.pk') }}</a>
        </p>
    </div>
</body>
</html>
