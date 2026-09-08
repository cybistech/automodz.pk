# {{ $siteName }}

> {{ $description }}

## Site

- Homepage: {{ $siteUrl }}/
- All products: {{ $siteUrl }}/products
- HTML sitemap: {{ $siteUrl }}/sitemap
- XML sitemap: {{ $siteUrl }}/sitemap.xml

## About

{{ $siteName }} ({{ parse_url($siteUrl, PHP_URL_HOST) }}) is a Pakistan-based e-commerce store for auto and motorcycle parts, performance mods, lights, mirrors, and accessories. Office: {{ config('site.office_city') }}, {{ config('site.office_country') }}. Prices are in PKR. Payments: EasyPaisa and cash on delivery. WhatsApp support: {{ config('site.whatsapp_display') }}.

## Crawling

- Product pages: {{ $siteUrl }}/products/{slug}
- Category filters: {{ $siteUrl }}/products?category={slug}
- Machine-readable sitemap: {{ $siteUrl }}/sitemap.xml
- Product RSS feed: {{ $siteUrl }}/feed.xml
