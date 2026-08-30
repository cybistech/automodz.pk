<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} - AutoPartsPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 font-bold text-white">AP</div>
            <div>
                <div class="text-xl font-bold">AutoParts<span class="text-orange-500">Pro</span></div>
                <div class="text-xs text-slate-400">Login to your account</div>
            </div>
        </a>

        <div class="card w-full max-w-md p-6">
            {{ $slot }}
        </div>

        <p class="mt-6 text-sm text-slate-400">
            <a href="{{ route('home') }}" class="text-orange-400 hover:text-orange-300">← Back to store</a>
        </p>
    </div>
</body>
</html>
