{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteName }} — Latest Products</title>
        <link>{{ $siteUrl }}/products</link>
        <description>{{ config('site.description') }}</description>
        <language>en-pk</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('feed.products') }}" rel="self" type="application/rss+xml"/>
        @foreach($products as $product)
            <item>
                <title>{{ $product->name }}</title>
                <link>{{ route('products.show', $product->slug) }}</link>
                <guid isPermaLink="true">{{ route('products.show', $product->slug) }}</guid>
                <description>{{ \App\Support\Seo::description($product->meta_description ?: $product->short_description) }}</description>
                <pubDate>{{ $product->updated_at->toRfc2822String() }}</pubDate>
                @if($product->category)
                    <category>{{ $product->category->name }}</category>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
