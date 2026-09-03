@props(['size' => 'md', 'showTagline' => true, 'iconOnly' => false])

@php
    $heights = [
        'xs' => 'h-7',
        'sm' => 'h-9',
        'md' => 'h-11',
        'lg' => 'h-14',
        'xl' => 'h-20',
    ];
    $taglines = [
        'xs' => 'text-[10px]',
        'sm' => 'text-xs',
        'md' => 'text-xs',
        'lg' => 'text-sm',
        'xl' => 'text-base',
    ];
    $height = $heights[$size] ?? $heights['md'];
    $taglineSize = $taglines[$size] ?? $taglines['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex flex-col overflow-visible']) }}>
    <img
        src="{{ $iconOnly ? '/favicon.svg' : '/images/logo.svg' }}"
        alt="{{ config('site.name') }}"
        class="{{ $height }} w-auto max-w-none shrink-0 object-contain object-left"
        width="300"
        height="72"
        decoding="async"
    >
    @if($showTagline && ! $iconOnly)
        <p class="{{ $taglineSize }} mt-1.5 font-medium leading-snug text-slate-400">{{ config('site.tagline') }}</p>
    @endif
</div>
