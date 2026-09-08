@extends('layouts.shop')

@section('title', $activeCategory?->name ?? 'Products')
@section('meta_title', $seoTitle)
@section('meta_description', $seoDescription)
@section('meta_keywords', $seoKeywords)
@section('canonical', \App\Support\Seo::listingCanonical(request()))
@if(request()->filled('search'))
@section('robots', 'noindex, follow')
@endif

@push('head')
    @if(!$products->onFirstPage())
        <link rel="prev" href="{{ $products->previousPageUrl() }}">
    @endif
    @if($products->hasMorePages())
        <link rel="next" href="{{ $products->nextPageUrl() }}">
    @endif
@endpush

@push('jsonld')
    @php
        $breadcrumbItems = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => route('products.index')],
        ];
        if ($activeCategory) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $activeCategory->name,
                'item' => route('products.index', ['category' => $activeCategory->slug]),
            ];
        }
        $listItems = $products->map(fn ($product, $index) => [
            '@type' => 'ListItem',
            'position' => (($products->currentPage() - 1) * $products->perPage()) + $index + 1,
            'url' => route('products.show', $product->slug),
            'name' => $product->name,
        ])->values()->all();
    @endphp
    <x-json-ld :data="[
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ]" />
    @if(count($listItems) > 0)
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $activeCategory?->name ?? 'Auto & Motorcycle Parts',
            'url' => \App\Support\Seo::listingCanonical(request()),
            'numberOfItems' => $products->total(),
            'itemListElement' => $listItems,
        ]" />
    @endif
@endpush

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <x-breadcrumbs :items="array_filter([
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Products', 'url' => route('products.index')],
        $activeCategory ? ['label' => $activeCategory->name, 'url' => route('products.index', ['category' => $activeCategory->slug])] : null,
    ])" />

    <div class="flex flex-col gap-8 lg:flex-row">
        <aside class="lg:w-64 flex-shrink-0">
            <div class="card p-5">
                <h2 class="font-semibold text-white">Filters</h2>
                <form method="GET" class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm text-slate-400" for="filter-category">Category</label>
                        <select id="filter-category" name="category" class="input-field mt-1" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400" for="filter-brand">Brand</label>
                        <select id="filter-brand" name="brand" class="input-field mt-1" onchange="this.form.submit()">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400" for="filter-sort">Sort By</label>
                        <select id="filter-sort" name="sort" class="input-field mt-1" onchange="this.form.submit()">
                            <option value="latest" @selected(request('sort', 'latest') === 'latest')>Latest</option>
                            <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                            <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
                            <option value="name" @selected(request('sort') === 'name')>Name</option>
                        </select>
                    </div>
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </form>
            </div>
        </aside>

        <div class="flex-1">
            <header>
                <h1 class="text-2xl font-bold text-white">
                    @if(request('search'))
                        Results for "{{ request('search') }}"
                    @elseif($activeCategory)
                        {{ $activeCategory->name }} — Auto & Motorcycle Parts
                    @else
                        Auto & Motorcycle Parts — Shop Online Pakistan
                    @endif
                </h1>
                @if($activeCategory?->description && ! request('search'))
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-400">{{ $activeCategory->description }}</p>
                @elseif(! request('search'))
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-400">
                        Browse genuine auto and motorcycle mods, lights, mirrors, and accessories at {{ config('site.domain') }}.
                        Fast delivery across Pakistan from {{ config('site.office_city') }}. EasyPaisa and cash on delivery accepted.
                    </p>
                @endif
                <p class="mt-2 text-sm text-slate-500">{{ $products->total() }} products</p>
            </header>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($products as $product)
                    <x-product-card :product="$product" :lazy="$loop->index >= 3" />
                @empty
                    <div class="col-span-full card p-12 text-center">
                        <p class="text-slate-400">No products found. Try adjusting your filters.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
