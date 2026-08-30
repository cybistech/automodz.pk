@extends('layouts.shop')

@section('title', $product->name)
@section('meta_title', $product->meta_title ?: $product->name.' | Buy Online Pakistan')
@section('meta_description', $product->meta_description ?: $product->short_description)
@section('meta_keywords', $product->meta_keywords)

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 text-sm text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-orange-400">Home</a> /
        <a href="{{ route('products.index') }}" class="hover:text-orange-400">Products</a> /
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-orange-400">{{ $product->category->name }}</a> /
        <span class="text-slate-300">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-2">
        <div>
            <div class="card overflow-hidden">
                @if($product->primary_image)
                    <img src="{{ asset('storage/'.$product->primary_image) }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                @else
                    <div class="flex aspect-square items-center justify-center bg-slate-900 text-slate-600">
                        <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            @if($product->images && count($product->images) > 1)
                <div class="mt-4 grid grid-cols-4 gap-3">
                    @foreach($product->images as $image)
                        <img src="{{ asset('storage/'.$image) }}" alt="" class="aspect-square rounded-lg border border-slate-700 object-cover">
                    @endforeach
                </div>
            @endif

            @if($product->video_source)
                <div class="card mt-6 overflow-hidden p-4">
                    <h3 class="mb-3 font-semibold">Product Video</h3>
                    @if($product->video_path)
                        <video controls class="w-full rounded-lg" poster="{{ $product->primary_image ? asset('storage/'.$product->primary_image) : '' }}">
                            <source src="{{ $product->video_source }}" type="video/mp4">
                            Your browser does not support video playback.
                        </video>
                    @else
                        @php
                            $videoUrl = $product->video_url;
                            $embedUrl = $videoUrl;
                            if (str_contains($videoUrl, 'youtube.com/watch')) {
                                parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);
                                $embedUrl = 'https://www.youtube.com/embed/'.($params['v'] ?? '');
                            } elseif (str_contains($videoUrl, 'youtu.be/')) {
                                $embedUrl = 'https://www.youtube.com/embed/'.basename(parse_url($videoUrl, PHP_URL_PATH));
                            }
                        @endphp
                        <div class="aspect-video overflow-hidden rounded-lg">
                            <iframe src="{{ $embedUrl }}" class="h-full w-full" allowfullscreen></iframe>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div>
            @if($product->brand)
                <span class="badge bg-orange-500/20 text-orange-400">{{ $product->brand }}</span>
            @endif
            <h1 class="mt-2 text-3xl font-bold text-white">{{ $product->name }}</h1>
            <p class="mt-1 text-sm text-slate-400">SKU: {{ $product->sku }} | Part #: {{ $product->part_number ?? 'N/A' }}</p>

            <div class="mt-6 flex items-baseline gap-3 flex-wrap">
                @if($product->sale_price)
                    <span class="text-3xl font-bold text-orange-400">Rs. {{ number_format($product->sale_price) }}</span>
                    <span class="text-lg text-slate-500 line-through">Rs. {{ number_format($product->price) }}</span>
                    @if($product->discount_percent)
                        <span class="badge bg-red-500 text-white">{{ $product->discount_percent }}% OFF</span>
                    @endif
                @else
                    <span class="text-3xl font-bold text-orange-400">Rs. {{ number_format($product->price) }}</span>
                @endif
            </div>

            <p class="mt-4 text-slate-300">{{ $product->short_description }}</p>

            <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                <div class="card p-3"><span class="text-slate-400">Condition</span><p class="font-medium capitalize">{{ $product->condition }}</p></div>
                <div class="card p-3"><span class="text-slate-400">Stock</span><p class="font-medium {{ $product->isInStock() ? 'text-green-400' : 'text-red-400' }}">{{ $product->stock }} available</p></div>
                @if($product->vehicle_make)
                    <div class="card p-3"><span class="text-slate-400">Vehicle</span><p class="font-medium">{{ $product->vehicle_make }} {{ $product->vehicle_model }}</p></div>
                @endif
                @if($product->warranty)
                    <div class="card p-3"><span class="text-slate-400">Warranty</span><p class="font-medium">{{ $product->warranty }}</p></div>
                @endif
            </div>

            @if($product->isInStock())
                <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-8 flex items-center gap-4">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="input-field w-20">
                    <button type="submit" class="btn-primary flex-1">Add to Cart</button>
                </form>
            @else
                <button disabled class="mt-8 w-full cursor-not-allowed rounded-lg bg-slate-700 px-5 py-3 font-semibold text-slate-400">Out of Stock</button>
            @endif

            @if($product->description)
                <div class="mt-8">
                    <h3 class="font-semibold text-white">Description</h3>
                    <p class="mt-2 text-slate-300 whitespace-pre-line">{{ $product->description }}</p>
                </div>
            @endif

            @if($product->specifications)
                <div class="mt-8">
                    <h3 class="font-semibold text-white">Specifications</h3>
                    <dl class="mt-3 space-y-2">
                        @foreach($product->specifications as $key => $value)
                            <div class="flex justify-between border-b border-slate-800 py-2 text-sm">
                                <dt class="text-slate-400">{{ $key }}</dt>
                                <dd class="font-medium">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-2xl font-bold text-white">Related Products</h2>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
