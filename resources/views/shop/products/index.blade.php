@extends('layouts.shop')

@section('title', $activeCategory?->name ?? 'Products')
@section('meta_title', $seoTitle)
@section('meta_description', $seoDescription)
@section('meta_keywords', $seoKeywords)
@section('canonical', \App\Support\Seo::listingCanonical(request()))
@section('og_type', 'website')
@section('meta_image', $seoImage)
@section('meta_image_alt', $seoImageAlt)
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
        <x-shop-filters
            :categories="$categories"
            :category-counts="$categoryCounts"
            :brands="$brands"
            :brand-counts="$brandCounts"
            :active-category="$activeCategory"
            :active-filters="$activeFilters"
        />

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

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <p class="text-sm text-slate-500">
                        <span class="font-semibold text-slate-300">{{ $products->total() }}</span>
                        {{ Str::plural('product', $products->total()) }}
                        @if(request('brand'))
                            · <span class="text-orange-300">{{ request('brand') }}</span>
                        @endif
                    </p>
                </div>

                @if($activeFilters->isNotEmpty())
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @foreach($activeFilters as $filter)
                            @php
                                $chipQuery = request()->except([$filter['key'], 'page']);
                            @endphp
                            <a href="{{ route('products.index', $chipQuery) }}" class="filter-chip" title="Remove {{ $filter['label'] }} filter">
                                <span class="text-orange-400/80">{{ $filter['label'] }}:</span>
                                <span>{{ $filter['value'] }}</span>
                                <svg class="h-3.5 w-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endforeach
                        <a href="{{ route('products.index', array_filter(['search' => request('search')])) }}" class="text-xs font-semibold text-slate-400 underline-offset-2 transition hover:text-orange-400 hover:underline">
                            Clear all
                        </a>
                    </div>
                @endif
            </header>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($products as $product)
                    <x-product-card :product="$product" :lazy="$loop->index >= 3" />
                @empty
                    <div class="col-span-full card p-12 text-center">
                        <p class="text-slate-400">No products found. Try adjusting your filters.</p>
                        <a href="{{ route('products.index') }}" class="btn-secondary mt-6 inline-flex">Reset filters</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
