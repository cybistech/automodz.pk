@php
    $metaTitle = trim($__env->yieldContent('meta_title', trim(View::getSection('title', config('site.name')).' | '.config('site.domain'))));
    $metaDescription = trim($__env->yieldContent('meta_description', config('site.description')));
    $canonical = trim($__env->yieldContent('canonical', rtrim(config('site.url'), '/').request()->getPathInfo()));
    $ogType = trim($__env->yieldContent('og_type', 'website')) ?: 'website';
    $metaImageRaw = trim($__env->yieldContent('meta_image'));
    $metaImageAlt = trim($__env->yieldContent('meta_image_alt'));
    $og = \App\Support\Seo::ogImage($metaImageRaw !== '' ? $metaImageRaw : null, $metaImageAlt !== '' ? $metaImageAlt : null);
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
@else
    <meta name="keywords" content="automodz, auto mods Pakistan, motorcycle parts, bike accessories, car mods, {{ config('site.domain') }}">
@endif
@if(config('seo.google_site_verification'))
    <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
@endif
<meta name="geo.region" content="PK-PB">
<meta name="geo.placename" content="{{ config('site.office_city') }}">
<meta name="author" content="{{ config('site.name') }}">
<meta name="theme-color" content="#020617">
<meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="{{ config('site.name') }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:locale" content="en_PK">
<meta property="og:locale:alternate" content="en_US">
<meta property="og:image" content="{{ $og['url'] }}">
<meta property="og:image:secure_url" content="{{ $og['url'] }}">
@if($og['type'])
    <meta property="og:image:type" content="{{ $og['type'] }}">
@endif
@if($og['width'])
    <meta property="og:image:width" content="{{ $og['width'] }}">
@endif
@if($og['height'])
    <meta property="og:image:height" content="{{ $og['height'] }}">
@endif
<meta property="og:image:alt" content="{{ $og['alt'] }}">

{{-- Twitter / X --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $og['url'] }}">
<meta name="twitter:image:alt" content="{{ $og['alt'] }}">
<meta name="twitter:url" content="{{ $canonical }}">
