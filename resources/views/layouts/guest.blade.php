<!DOCTYPE html>
<html lang="en-PK">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $guestTitle = ($title ?? 'Account').' | '.config('site.name');
        $guestDescription = config('site.description');
        $guestUrl = url()->current();
        $guestOg = \App\Support\Seo::ogImage(null, config('site.name').' — '.config('site.tagline'));
    @endphp
    <title>{{ $guestTitle }}</title>
    <meta name="description" content="{{ $guestDescription }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="{{ config('site.name') }}">
    <meta name="theme-color" content="#020617">
    <link rel="canonical" href="{{ $guestUrl }}">
    <meta property="og:title" content="{{ $guestTitle }}">
    <meta property="og:description" content="{{ $guestDescription }}">
    <meta property="og:url" content="{{ $guestUrl }}">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_PK">
    <meta property="og:image" content="{{ $guestOg['url'] }}">
    <meta property="og:image:secure_url" content="{{ $guestOg['url'] }}">
    <meta property="og:image:type" content="{{ $guestOg['type'] }}">
    <meta property="og:image:width" content="{{ $guestOg['width'] }}">
    <meta property="og:image:height" content="{{ $guestOg['height'] }}">
    <meta property="og:image:alt" content="{{ $guestOg['alt'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $guestTitle }}">
    <meta name="twitter:description" content="{{ $guestDescription }}">
    <meta name="twitter:image" content="{{ $guestOg['url'] }}">
    <meta name="twitter:image:alt" content="{{ $guestOg['alt'] }}">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="image_src" href="{{ $guestOg['url'] }}">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,600,700|rajdhani:700&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <a href="{{ route('home') }}" class="mb-8">
            <x-brand-logo size="lg" />
        </a>

        <div class="card w-full max-w-md p-6">
            {{ $slot }}
        </div>

        <p class="mt-6 text-sm text-slate-400">
            <a href="{{ route('home') }}" class="text-orange-400 hover:text-orange-300">← Back to {{ config('site.domain') }}</a>
        </p>
    </div>
</body>
</html>
