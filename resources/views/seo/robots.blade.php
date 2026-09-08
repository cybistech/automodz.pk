# {{ $siteUrl }} — AutoModz.pk
User-agent: *
Allow: /
@foreach($disallowed as $path)
Disallow: /{{ $path }}
@endforeach

@foreach($aiCrawlers as $bot)
User-agent: {{ $bot }}
Allow: /
@foreach($disallowed as $path)
Disallow: /{{ $path }}
@endforeach

@endforeach
Sitemap: {{ $siteUrl }}/sitemap.xml

# Product RSS feed
# {{ $siteUrl }}/feed.xml
