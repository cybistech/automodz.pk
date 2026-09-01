@props(['class' => 'h-10 w-10'])

@php($gid = uniqid('am-'))

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
        <linearGradient id="{{ $gid }}-gradient" x1="8" y1="4" x2="56" y2="60" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FBBF24"/>
            <stop offset="0.5" stop-color="#F97316"/>
            <stop offset="1" stop-color="#EF4444"/>
        </linearGradient>
        <linearGradient id="{{ $gid }}-shine" x1="20" y1="8" x2="44" y2="40" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FFFFFF" stop-opacity="0.35"/>
            <stop offset="1" stop-color="#FFFFFF" stop-opacity="0"/>
        </linearGradient>
    </defs>
    <rect x="4" y="4" width="56" height="56" rx="16" fill="#0F172A"/>
    <rect x="4.5" y="4.5" width="55" height="55" rx="15.5" stroke="url(#{{ $gid }}-gradient)" stroke-opacity="0.45"/>
    <path d="M14 42L22 18H28L36 42H31L29.5 37H20.5L19 42H14ZM21.5 33H28.5L25 22.5L21.5 33Z" fill="url(#{{ $gid }}-gradient)"/>
    <path d="M38 18H43L50 30V18H55V42H50L43 30V42H38V18Z" fill="url(#{{ $gid }}-gradient)"/>
    <path d="M46 8L58 20" stroke="url(#{{ $gid }}-gradient)" stroke-width="2.5" stroke-linecap="round"/>
    <path d="M50 8L62 20" stroke="url(#{{ $gid }}-gradient)" stroke-width="2" stroke-linecap="round" opacity="0.55"/>
    <path d="M54 8L64 18" stroke="url(#{{ $gid }}-gradient)" stroke-width="1.5" stroke-linecap="round" opacity="0.35"/>
    <ellipse cx="26" cy="24" rx="10" ry="6" fill="url(#{{ $gid }}-shine)" transform="rotate(-18 26 24)"/>
</svg>
