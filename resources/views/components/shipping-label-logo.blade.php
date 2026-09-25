@php($uid = 'sll-' . uniqid())

<div class="sl-site-logo">
    <svg class="sl-site-logo-mark" viewBox="0 0 296 64" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{{ config('site.name') }}">
        <defs>
            <linearGradient id="{{ $uid }}-fire" x1="70" y1="32" x2="290" y2="32" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FBBF24"/>
                <stop offset="0.55" stop-color="#F97316"/>
                <stop offset="1" stop-color="#DC2626"/>
            </linearGradient>
            <linearGradient id="{{ $uid }}-icon" x1="6" y1="4" x2="58" y2="60" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FEF3C7"/>
                <stop offset="0.4" stop-color="#FBBF24"/>
                <stop offset="0.75" stop-color="#F97316"/>
                <stop offset="1" stop-color="#DC2626"/>
            </linearGradient>
            <linearGradient id="{{ $uid }}-shine" x1="14" y1="10" x2="42" y2="34" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFFFFF" stop-opacity="0.4"/>
                <stop offset="1" stop-color="#FFFFFF" stop-opacity="0"/>
            </linearGradient>
        </defs>

        <rect x="0.75" y="0.75" width="62.5" height="62.5" rx="15" fill="#030712"/>
        <rect x="0.75" y="0.75" width="62.5" height="62.5" rx="15" stroke="url(#{{ $uid }}-icon)" stroke-width="1.5"/>
        <circle cx="32" cy="46" r="12" stroke="url(#{{ $uid }}-icon)" stroke-width="1.15" opacity="0.4"/>
        <circle cx="32" cy="46" r="7.5" stroke="url(#{{ $uid }}-icon)" stroke-width="0.75" opacity="0.22"/>
        <path d="M32 34v24M20 46h24M24 38l16 16M40 38l-16 16" stroke="url(#{{ $uid }}-icon)" stroke-width="0.95" stroke-linecap="round" opacity="0.35"/>
        <path d="M14.5 43.5 23 17h5.1L36.5 43.5h-4.9l-1.55-4.55H20.95l-1.55 4.55H14.5Zm7.35-9.1h8.35L25.75 21.2 21.85 34.4Z" fill="url(#{{ $uid }}-icon)" fill-rule="evenodd"/>
        <path d="M38 43.5V17h4.15l4.25 12.7L50.5 17H54.6v26.5h-4.1V29.7L46.1 42 41.7 29.7v13.8H38Z" fill="url(#{{ $uid }}-icon)"/>
        <path d="M43 10.5 52 19.5" stroke="url(#{{ $uid }}-icon)" stroke-width="2.4" stroke-linecap="round"/>
        <path d="M46.5 9.8 54.5 17.8" stroke="url(#{{ $uid }}-icon)" stroke-width="1.7" stroke-linecap="round" opacity="0.5"/>
        <ellipse cx="24" cy="25" rx="8.5" ry="4.5" fill="url(#{{ $uid }}-shine)" transform="rotate(-16 24 25)"/>

        <text x="72" y="41" font-family="Rajdhani, Arial Narrow, Arial, sans-serif" font-weight="700" font-size="38" fill="#111111" letter-spacing="-1">Auto</text>
        <text x="141" y="41" font-family="Rajdhani, Arial Narrow, Arial, sans-serif" font-weight="700" font-size="38" fill="url(#{{ $uid }}-fire)" letter-spacing="-1">Modz</text>
        <path d="M141 48h92" stroke="url(#{{ $uid }}-fire)" stroke-width="2" stroke-linecap="round" opacity="0.85"/>

        <rect x="240" y="20" width="48" height="24" rx="8" fill="url(#{{ $uid }}-fire)"/>
        <rect x="241" y="21" width="46" height="22" rx="7" stroke="#FFFFFF" stroke-opacity="0.2" stroke-width="0.7"/>
        <text x="249" y="38" font-family="Rajdhani, Arial Narrow, Arial, sans-serif" font-weight="700" font-size="18" fill="#0A0F1A" letter-spacing="0.5">.pk</text>
    </svg>
    <p class="sl-site-logo-categories">Auto Parts | Accessories | Car Care</p>
    <p class="sl-site-logo-tagline">Drive Your Style</p>
</div>
