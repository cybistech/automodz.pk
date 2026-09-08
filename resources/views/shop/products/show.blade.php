@extends('layouts.shop')

@section('title', $product->name)
@section('meta_title', $product->meta_title ?: $product->name.' | Buy Online Pakistan')
@section('meta_description', \App\Support\Seo::description($product->meta_description ?: $product->short_description ?: $product->description))
@section('meta_keywords', $product->meta_keywords)
@section('og_type', 'product')
@section('canonical', route('products.show', $product->slug))
@section('meta_image', \App\Support\Seo::absolute($product->imageUrl() ?: \App\Support\Seo::mainBanner()['src']))
@section('meta_image_alt', $product->primary_image ? $product->imageAlt() : \App\Support\Seo::mainBanner()['alt'])

@push('head')
    <meta property="product:price:amount" content="{{ number_format($product->effective_price, 2, '.', '') }}">
    <meta property="product:price:currency" content="PKR">
    <meta property="product:availability" content="{{ $product->isInStock() ? 'in stock' : 'out of stock' }}">
    <meta property="product:condition" content="{{ $product->condition }}">
    @if($product->brand)
        <meta property="product:brand" content="{{ $product->brand }}">
    @endif
    @if($product->rating_count > 0)
        <meta property="product:rating:value" content="{{ number_format((float) $product->rating_avg, 1, '.', '') }}">
        <meta property="product:rating:scale" content="5">
        <meta property="product:rating:count" content="{{ $product->rating_count }}">
    @endif
@endpush

@push('jsonld')
    @php
        $productSchema = \App\Support\ProductSchema::forProduct($product, $product->approvedReviews);
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => route('products.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name, 'item' => route('products.index', ['category' => $product->category->slug])],
                ['@type' => 'ListItem', 'position' => 4, 'name' => $product->name, 'item' => route('products.show', $product->slug)],
            ],
        ];
    @endphp
    <x-json-ld :data="$productSchema" />
    <x-json-ld :data="$breadcrumbSchema" />
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 text-sm text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-orange-400">Home</a> /
        <a href="{{ route('products.index') }}" class="hover:text-orange-400">Products</a> /
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-orange-400">{{ $product->category->name }}</a> /
        <span class="text-slate-300">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-2">
        @php
            $gallery = collect($product->images ?? [])->values()->map(function ($image, $index) use ($product) {
                return [
                    'type' => 'image',
                    'src' => $product->imageUrl($image),
                    'thumb' => $product->imageUrl($image, true),
                    'alt' => $product->imageAlt($index),
                ];
            })->all();

            if ($product->video_path) {
                $gallery[] = [
                    'type' => 'video',
                    'src' => $product->video_source,
                    'thumb' => $product->imageUrl(),
                    'alt' => $product->name.' video',
                ];
            } elseif ($product->videoEmbedUrl()) {
                $gallery[] = [
                    'type' => 'embed',
                    'src' => $product->videoEmbedUrl(),
                    'thumb' => $product->imageUrl(),
                    'alt' => $product->name.' video',
                ];
            }
        @endphp

        <div>
            @if(count($gallery))
                <div id="product-gallery" class="mx-auto flex max-w-lg flex-col gap-2.5 sm:max-w-xl">
                    <div class="relative min-w-0">
                        <div class="group relative overflow-hidden">
                            <button type="button" id="gallery-stage-image-wrap" class="block w-full cursor-zoom-in {{ ($gallery[0]['type'] ?? '') === 'image' ? '' : 'hidden' }}" aria-label="View larger image">
                                <img
                                    id="gallery-stage-image"
                                    src="{{ $gallery[0]['src'] }}"
                                    alt="{{ $gallery[0]['alt'] }}"
                                    title="{{ $gallery[0]['alt'] }}"
                                    width="576"
                                    height="576"
                                    fetchpriority="high"
                                    decoding="async"
                                    class="mx-auto aspect-square max-h-96 w-full object-contain sm:max-h-[28rem]"
                                >
                            </button>
                            <video
                                id="gallery-stage-video"
                                class="mx-auto aspect-square max-h-96 w-full object-contain sm:max-h-[28rem] {{ ($gallery[0]['type'] ?? '') === 'video' ? '' : 'hidden' }}"
                                controls
                                playsinline
                                poster="{{ $product->imageUrl() ?? '' }}"
                                @if(($gallery[0]['type'] ?? '') === 'video') src="{{ $gallery[0]['src'] }}" @endif
                            ></video>
                            <div id="gallery-stage-embed" class="mx-auto aspect-square max-h-96 w-full sm:max-h-[28rem] {{ ($gallery[0]['type'] ?? '') === 'embed' ? '' : 'hidden' }}">
                                @if(($gallery[0]['type'] ?? '') === 'embed')
                                    <iframe src="{{ $gallery[0]['src'] }}" class="h-full w-full" title="{{ $gallery[0]['alt'] }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(count($gallery) > 1)
                        <div class="flex justify-center gap-1.5 overflow-x-auto" role="tablist" aria-label="Product media">
                            @foreach($gallery as $index => $item)
                                <button
                                    type="button"
                                    class="gallery-thumb relative h-14 w-14 shrink-0 overflow-hidden rounded-md border-2 sm:h-16 sm:w-16 {{ $index === 0 ? 'border-orange-500' : 'border-slate-700 hover:border-slate-500' }}"
                                    data-index="{{ $index }}"
                                    aria-label="{{ $item['type'] === 'image' ? $item['alt'] : 'Play product video' }}"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                >
                                    @if($item['thumb'])
                                        <img src="{{ $item['thumb'] }}" alt="" width="64" height="64" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                    @else
                                        <span class="flex h-full w-full items-center justify-center bg-slate-900 text-slate-500">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                        </span>
                                    @endif
                                    @if($item['type'] !== 'image')
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/40">
                                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-orange-500 text-white">
                                                <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                            </span>
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div id="gallery-lightbox" class="fixed inset-0 z-[80] hidden" role="dialog" aria-modal="true" aria-label="Product media viewer">
                    <button type="button" id="gallery-backdrop" class="absolute inset-0 bg-slate-950/75 backdrop-blur-sm" aria-label="Close gallery"></button>
                    <div class="relative z-10 flex h-full flex-col items-center justify-center p-4 pt-14 pb-28">
                        <button type="button" id="gallery-close" class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white hover:bg-white/20" aria-label="Close">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <button type="button" id="gallery-prev" class="absolute left-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white hover:bg-white/20 sm:left-6" aria-label="Previous">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div id="gallery-lightbox-stage" class="flex max-h-[78vh] w-full max-w-6xl flex-1 items-center justify-center"></div>
                        <button type="button" id="gallery-next" class="absolute right-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white hover:bg-white/20 sm:right-6" aria-label="Next">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="absolute inset-x-0 bottom-0 flex flex-col items-center gap-2 px-4 pb-4 pt-8">
                            <p id="gallery-counter" class="rounded-full bg-black/60 px-3 py-1 text-xs text-white"></p>
                            @if(count($gallery) > 1)
                                <div id="gallery-lightbox-thumbs" class="flex max-w-full gap-1.5 overflow-x-auto">
                                    @foreach($gallery as $index => $item)
                                        <button
                                            type="button"
                                            class="lightbox-thumb relative h-10 w-10 shrink-0 overflow-hidden rounded border-2 {{ $index === 0 ? 'border-orange-500' : 'border-white/30' }}"
                                            data-index="{{ $index }}"
                                            aria-label="Show media {{ $index + 1 }}"
                                        >
                                            @if($item['thumb'])
                                                <img src="{{ $item['thumb'] }}" alt="" width="40" height="40" class="h-full w-full object-cover">
                                            @else
                                                <span class="flex h-full w-full items-center justify-center bg-slate-800 text-white">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                                </span>
                                            @endif
                                            @if($item['type'] !== 'image')
                                                <span class="absolute inset-0 flex items-center justify-center bg-black/40">
                                                    <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                                </span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <script type="application/json" id="product-gallery-data">@json($gallery)</script>
            @else
                <div class="card overflow-hidden">
                    <div class="flex aspect-square items-center justify-center bg-slate-900 text-slate-600">
                        <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            @endif
        </div>

        <div>
            @if($product->brand)
                <span class="badge bg-orange-500/20 text-orange-400">{{ $product->brand }}</span>
            @endif
            <h1 class="mt-2 text-3xl font-bold text-white">{{ $product->name }}</h1>
            <p class="mt-1 text-sm text-slate-400">SKU: {{ $product->sku }} | Part #: {{ $product->part_number ?? 'N/A' }}</p>

            @if($product->rating_count > 0)
                <div class="mt-3">
                    <a href="#reviews" class="inline-flex items-center gap-2 hover:opacity-90">
                        <x-star-rating :rating="$product->rating_avg" :count="$product->rating_count" size="lg" />
                    </a>
                </div>
            @endif

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
                <div class="card p-3"><span class="text-slate-400">Stock</span><p class="font-medium {{ $product->isInStock() ? 'text-green-400' : 'text-red-400' }}">{{ $product->isInStock() ? $product->stock.' available' : 'Out of stock' }}</p></div>
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

    <section id="reviews" class="mt-16 scroll-mt-24">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Customer Reviews</h2>
                @if($product->rating_count > 0)
                    <div class="mt-2">
                        <x-star-rating :rating="$product->rating_avg" :count="$product->rating_count" size="lg" />
                    </div>
                @else
                    <p class="mt-2 text-sm text-slate-400">Be the first to review this product.</p>
                @endif
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <div class="card p-6">
                    <h3 class="font-semibold text-white">Write a review</h3>
                    @if($userReview)
                        <p class="mt-3 text-sm text-green-400">You already reviewed this product. Thank you!</p>
                    @else
                        <form action="{{ route('products.reviews.store', $product->slug) }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="text-sm text-slate-400">Your rating *</label>
                                <div class="mt-2 flex gap-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" @checked((int) old('rating', 5) === $i) required>
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-700 text-sm text-slate-400 peer-checked:border-amber-400 peer-checked:bg-amber-400/20 peer-checked:text-amber-300">{{ $i }}★</span>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm text-slate-400">Name *</label>
                                <input type="text" name="author_name" value="{{ old('author_name', auth()->user()->name ?? '') }}" required class="input-field mt-1" maxlength="100">
                                @error('author_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm text-slate-400">Email *</label>
                                <input type="email" name="author_email" value="{{ old('author_email', auth()->user()->email ?? '') }}" required class="input-field mt-1" maxlength="255">
                                <p class="mt-1 text-xs text-slate-500">Not shown publicly. Used to prevent duplicate reviews.</p>
                                @error('author_email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm text-slate-400">Your review *</label>
                                <textarea name="body" rows="4" required class="input-field mt-1" minlength="10" maxlength="2000" placeholder="Share your experience with this product...">{{ old('body') }}</textarea>
                                @error('body')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="btn-primary w-full">Submit Review</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-3 space-y-4">
                @forelse($product->approvedReviews as $review)
                    <article class="card p-5" itemscope itemtype="https://schema.org/Review">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-white" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    <span itemprop="name">{{ $review->author_name }}</span>
                                </p>
                            </div>
                            <div>
                                <x-star-rating :rating="$review->rating" />
                                <meta itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                <meta itemprop="ratingValue" content="{{ $review->rating }}">
                                <time class="mt-1 block text-xs text-slate-500" datetime="{{ $review->created_at->toDateString() }}" itemprop="datePublished">
                                    {{ $review->created_at->format('M d, Y') }}
                                </time>
                            </div>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300" itemprop="reviewBody">{{ $review->body }}</p>
                    </article>
                @empty
                    <div class="card p-8 text-center text-slate-400">No reviews yet.</div>
                @endforelse
            </div>
        </div>
    </section>

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

@push('scripts')
<script>
(function () {
    const dataEl = document.getElementById('product-gallery-data');
    const lightbox = document.getElementById('gallery-lightbox');
    if (!dataEl || !lightbox) return;

    const items = JSON.parse(dataEl.textContent || '[]');
    if (!items.length) return;

    let current = 0;
    const imageWrap = document.getElementById('gallery-stage-image-wrap');
    const imageEl = document.getElementById('gallery-stage-image');
    const videoEl = document.getElementById('gallery-stage-video');
    const embedEl = document.getElementById('gallery-stage-embed');
    const lightboxStage = document.getElementById('gallery-lightbox-stage');
    const counter = document.getElementById('gallery-counter');
    const thumbs = Array.from(document.querySelectorAll('.gallery-thumb'));
    const lightboxThumbs = Array.from(document.querySelectorAll('.lightbox-thumb'));
    const prevBtn = document.getElementById('gallery-prev');
    const nextBtn = document.getElementById('gallery-next');
    const hasSlider = items.length > 1;
    let touchStartX = 0;

    function stopStageMedia() {
        if (videoEl) {
            videoEl.pause();
            videoEl.removeAttribute('src');
            videoEl.load();
        }
        if (embedEl) embedEl.innerHTML = '';
    }

    function select(index, openLightbox) {
        current = (index + items.length) % items.length;
        const item = items[current];

        thumbs.forEach((thumb, thumbIndex) => {
            const active = thumbIndex === current;
            thumb.setAttribute('aria-selected', active ? 'true' : 'false');
            thumb.classList.toggle('border-orange-500', active);
            thumb.classList.toggle('border-slate-700', !active);
        });

        lightboxThumbs.forEach((thumb, thumbIndex) => {
            const active = thumbIndex === current;
            thumb.classList.toggle('border-orange-500', active);
            thumb.classList.toggle('border-white/30', !active);
        });

        stopStageMedia();
        imageWrap?.classList.toggle('hidden', item.type !== 'image');
        videoEl?.classList.toggle('hidden', item.type !== 'video');
        embedEl?.classList.toggle('hidden', item.type !== 'embed');

        if (item.type === 'image' && imageEl) {
            imageEl.src = item.src;
            imageEl.alt = item.alt;
            imageEl.title = item.alt;
        } else if (item.type === 'video' && videoEl) {
            videoEl.src = item.src;
        } else if (item.type === 'embed' && embedEl) {
            const iframe = document.createElement('iframe');
            iframe.src = item.src;
            iframe.title = item.alt;
            iframe.className = 'h-full w-full';
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            embedEl.appendChild(iframe);
        }

        if (openLightbox) {
            open();
        } else if (!lightbox.classList.contains('hidden')) {
            renderLightbox();
        }
    }

    function renderLightbox() {
        const item = items[current];
        lightboxStage.replaceChildren();

        if (item.type === 'video') {
            const video = document.createElement('video');
            video.src = item.src;
            video.controls = true;
            video.autoplay = true;
            video.playsInline = true;
            video.className = 'max-h-[78vh] w-full max-w-6xl rounded-lg bg-black';
            lightboxStage.appendChild(video);
        } else if (item.type === 'embed') {
            const wrap = document.createElement('div');
            wrap.className = 'aspect-video w-full max-w-6xl';
            const iframe = document.createElement('iframe');
            iframe.src = item.src.includes('?') ? item.src + '&autoplay=1' : item.src + '?autoplay=1';
            iframe.title = item.alt;
            iframe.className = 'h-full w-full rounded-lg';
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('allow', 'autoplay; accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            wrap.appendChild(iframe);
            lightboxStage.appendChild(wrap);
        } else {
            const img = document.createElement('img');
            img.src = item.src;
            img.alt = item.alt;
            img.className = 'max-h-[78vh] w-auto max-w-full object-contain';
            lightboxStage.appendChild(img);
        }

        if (counter) counter.textContent = `${current + 1} / ${items.length}`;
        prevBtn?.classList.toggle('hidden', !hasSlider);
        nextBtn?.classList.toggle('hidden', !hasSlider);
    }

    function open() {
        renderLightbox();
        lightbox.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        document.getElementById('gallery-close')?.focus();
    }

    function close() {
        lightboxStage.innerHTML = '';
        lightbox.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => select(Number(thumb.dataset.index), true));
    });

    lightboxThumbs.forEach((thumb) => {
        thumb.addEventListener('click', (event) => {
            event.stopPropagation();
            select(Number(thumb.dataset.index), true);
        });
    });

    imageWrap?.addEventListener('click', open);
    document.getElementById('gallery-close')?.addEventListener('click', close);
    document.getElementById('gallery-backdrop')?.addEventListener('click', close);
    prevBtn?.addEventListener('click', (event) => {
        event.stopPropagation();
        select(current - 1, true);
    });
    nextBtn?.addEventListener('click', (event) => {
        event.stopPropagation();
        select(current + 1, true);
    });

    lightboxStage?.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0]?.screenX ?? 0;
    }, { passive: true });

    lightboxStage?.addEventListener('touchend', (event) => {
        if (!hasSlider) return;
        const delta = (event.changedTouches[0]?.screenX ?? 0) - touchStartX;
        if (Math.abs(delta) < 40) return;
        select(current + (delta < 0 ? 1 : -1), true);
    }, { passive: true });

    document.addEventListener('keydown', (event) => {
        if (lightbox.classList.contains('hidden')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft' && hasSlider) select(current - 1, true);
        if (event.key === 'ArrowRight' && hasSlider) select(current + 1, true);
    });
})();
</script>
@endpush
