@props(['size' => 'md', 'showTagline' => true])

@php
    $sizes = [
        'sm' => ['icon' => 'h-8 w-8', 'title' => 'text-base', 'domain' => 'text-[10px]', 'sub' => 'text-[10px]'],
        'md' => ['icon' => 'h-10 w-10', 'title' => 'text-xl', 'domain' => 'text-xs', 'sub' => 'text-xs'],
        'lg' => ['icon' => 'h-14 w-14', 'title' => 'text-2xl', 'domain' => 'text-sm', 'sub' => 'text-sm'],
        'xl' => ['icon' => 'h-20 w-20', 'title' => 'text-4xl', 'domain' => 'text-base', 'sub' => 'text-base'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <x-logo-icon :class="$s['icon'] . ' shrink-0'" />
    <div class="min-w-0">
        <div class="{{ $s['title'] }} font-extrabold tracking-tight leading-none">
            <span class="text-white">Auto</span><span class="bg-gradient-to-r from-amber-400 via-orange-500 to-red-500 bg-clip-text text-transparent">Modz</span><span class="font-semibold text-slate-500">.pk</span>
        </div>
        @if($showTagline)
            <div class="{{ $s['sub'] }} mt-0.5 font-medium text-slate-400">{{ config('site.tagline') }}</div>
        @endif
    </div>
</div>
