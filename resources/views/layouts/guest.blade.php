<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} - {{ config('site.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
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
