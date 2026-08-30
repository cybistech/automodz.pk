@props(['size' => 'md'])

@php
    $sizes = [
        'sm' => ['box' => 'h-8 w-8 text-sm', 'title' => 'text-base', 'sub' => 'text-[10px]'],
        'md' => ['box' => 'h-10 w-10 text-base', 'title' => 'text-lg', 'sub' => 'text-xs'],
        'lg' => ['box' => 'h-12 w-12 text-lg', 'title' => 'text-xl', 'sub' => 'text-xs'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="flex {{ $s['box'] }} items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 font-bold text-white">MM</div>
    <div>
        <div class="{{ $s['title'] }} font-bold tracking-tight">Moto<span class="text-orange-500">Modz</span></div>
        <div class="{{ $s['sub'] }} text-slate-400">{{ config('site.tagline') }}</div>
    </div>
</div>
