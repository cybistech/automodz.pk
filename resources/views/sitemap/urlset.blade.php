{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <lastmod>{{ ($url['lastmod'] ?? now())->toAtomString() }}</lastmod>
        <changefreq>{{ $url['changefreq'] ?? 'weekly' }}</changefreq>
        <priority>{{ $url['priority'] ?? '0.5' }}</priority>
        @foreach($url['images'] ?? [] as $image)
            <image:image>
                <image:loc>{{ $image['loc'] }}</image:loc>
                @if(! empty($image['title']))
                    <image:title>{{ $image['title'] }}</image:title>
                @endif
            </image:image>
        @endforeach
    </url>
@endforeach
</urlset>
