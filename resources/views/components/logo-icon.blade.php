@props(['class' => 'h-10 w-10'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
        <linearGradient id="am-gradient" x1="12" y1="8" x2="60" y2="64" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FBBF24"/>
            <stop offset="0.5" stop-color="#F97316"/>
            <stop offset="1" stop-color="#EA580C"/>
        </linearGradient>
        <linearGradient id="am-shine" x1="22" y1="16" x2="42" y2="36" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FFFFFF" stop-opacity="0.28"/>
            <stop offset="1" stop-color="#FFFFFF" stop-opacity="0"/>
        </linearGradient>
    </defs>
    <rect x="6" y="6" width="60" height="60" rx="14" fill="#0B1220"/>
    <rect x="6.5" y="6.5" width="59" height="59" rx="13.5" stroke="url(#am-gradient)" stroke-opacity="0.5"/>
    <path d="M16 50L24 26H29L37 50H32.5L31 45H22L20.5 50H16ZM23 40H30L26.5 29.5L23 40Z" fill="url(#am-gradient)"/>
    <path d="M39 50V26H43L47 38L51 26H55V50H51V34L47 46L43 34V50H39Z" fill="url(#am-gradient)"/>
    <path d="M44 14L56 26" stroke="url(#am-gradient)" stroke-width="2.5" stroke-linecap="round"/>
    <path d="M48 14L58 24" stroke="url(#am-gradient)" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
    <path d="M52 14L60 22" stroke="url(#am-gradient)" stroke-width="1.5" stroke-linecap="round" opacity="0.35"/>
    <ellipse cx="28" cy="32" rx="9" ry="5" fill="url(#am-shine)" transform="rotate(-16 28 32)"/>
</svg>
