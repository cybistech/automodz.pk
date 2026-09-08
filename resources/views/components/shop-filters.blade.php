@props([
    'categories',
    'categoryCounts',
    'brands',
    'brandCounts',
    'activeCategory' => null,
    'activeFilters',
])

@php
    $baseQuery = array_filter([
        'search' => request('search'),
        'category' => request('category'),
        'brand' => request('brand'),
        'sort' => request('sort', 'latest') !== 'latest' ? request('sort') : null,
    ], fn ($value) => $value !== null && $value !== '');

    $filterUrl = function (array $overrides = [], array $remove = []) use ($baseQuery) {
        $query = array_merge($baseQuery, $overrides);
        foreach ($remove as $key) {
            unset($query[$key]);
        }

        return route('products.index', array_filter($query, fn ($value) => $value !== null && $value !== ''));
    };

    $hasFilters = $activeFilters->isNotEmpty();
    $totalCategoryProducts = (int) $categoryCounts->sum();
@endphp

<aside class="lg:w-72 flex-shrink-0">
    <details id="shop-filters" class="group/filters card overflow-hidden lg:sticky lg:top-24">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 border-b border-slate-700/60 bg-gradient-to-r from-orange-500/10 via-transparent to-transparent px-5 py-4 marker:content-none [&::-webkit-details-marker]:hidden lg:pointer-events-none">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-orange-500/30 bg-orange-500/10 text-orange-400" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/></svg>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-white">Filters</h2>
                    <p class="text-xs text-slate-400">
                        @if($hasFilters)
                            {{ $activeFilters->count() }} active
                        @else
                            Refine your search
                        @endif
                    </p>
                </div>
            </div>
            <span class="rounded-lg border border-slate-600/70 px-2 py-1 text-xs font-semibold text-slate-300 transition group-open/filters:rotate-180 lg:hidden" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
        </summary>

        <div class="space-y-6 p-5">
            @if($hasFilters)
                <div class="flex items-center justify-between gap-2 rounded-xl border border-orange-500/20 bg-orange-500/5 px-3 py-2">
                    <span class="text-xs font-semibold uppercase tracking-wide text-orange-300">Active filters</span>
                    <a href="{{ route('products.index', array_filter(['search' => request('search')])) }}" class="text-xs font-semibold text-orange-400 transition hover:text-orange-300">
                        Clear all
                    </a>
                </div>
            @endif

            <section aria-label="Filter by category">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Category</h3>
                    @if(request('category'))
                        <a href="{{ $filterUrl([], ['category']) }}" class="text-xs text-slate-500 transition hover:text-orange-400">Reset</a>
                    @endif
                </div>
                <ul class="filter-list max-h-64 space-y-1 overflow-y-auto pr-1">
                    <li>
                        <a
                            href="{{ $filterUrl([], ['category']) }}"
                            class="filter-option {{ ! request('category') ? 'is-active' : '' }}"
                        >
                            <span class="filter-option__label">All Categories</span>
                            <span class="filter-option__count">{{ $totalCategoryProducts }}</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                        @php $count = (int) ($categoryCounts[$category->id] ?? 0); @endphp
                        <li>
                            <a
                                href="{{ $filterUrl(['category' => $category->slug], ['brand']) }}"
                                class="filter-option {{ request('category') === $category->slug ? 'is-active' : '' }}"
                                @if(request('category') === $category->slug) aria-current="true" @endif
                            >
                                <span class="filter-option__label">{{ $category->name }}</span>
                                <span class="filter-option__count">{{ $count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section aria-label="Filter by brand">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Brand</h3>
                    @if(request('brand'))
                        <a href="{{ $filterUrl([], ['brand']) }}" class="text-xs text-slate-500 transition hover:text-orange-400">Reset</a>
                    @endif
                </div>
                @if($brands->isEmpty())
                    <p class="rounded-xl border border-dashed border-slate-700 px-3 py-4 text-center text-xs text-slate-500">No brands in this category yet.</p>
                @else
                    <ul class="filter-list max-h-52 space-y-1 overflow-y-auto pr-1">
                        <li>
                            <a
                                href="{{ $filterUrl([], ['brand']) }}"
                                class="filter-option {{ ! request('brand') ? 'is-active' : '' }}"
                            >
                                <span class="filter-option__label">All Brands</span>
                                <span class="filter-option__count">{{ $brandCounts->sum() }}</span>
                            </a>
                        </li>
                        @foreach($brands as $brand)
                            <li>
                                <a
                                    href="{{ $filterUrl(['brand' => $brand]) }}"
                                    class="filter-option {{ request('brand') === $brand ? 'is-active' : '' }}"
                                    @if(request('brand') === $brand) aria-current="true" @endif
                                >
                                    <span class="filter-option__label">{{ $brand }}</span>
                                    <span class="filter-option__count">{{ (int) ($brandCounts[$brand] ?? 0) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section aria-label="Sort products">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Sort by</h3>
                <div class="grid grid-cols-1 gap-1.5">
                    @foreach([
                        'latest' => ['label' => 'Latest arrivals', 'hint' => 'Newest first'],
                        'price_low' => ['label' => 'Price: Low to High', 'hint' => 'Best deals first'],
                        'price_high' => ['label' => 'Price: High to Low', 'hint' => 'Premium first'],
                        'name' => ['label' => 'Name A–Z', 'hint' => 'Alphabetical'],
                    ] as $sortKey => $sortMeta)
                        <a
                            href="{{ $sortKey === 'latest' ? $filterUrl([], ['sort']) : $filterUrl(['sort' => $sortKey]) }}"
                            class="filter-sort {{ request('sort', 'latest') === $sortKey ? 'is-active' : '' }}"
                        >
                            <span class="font-medium">{{ $sortMeta['label'] }}</span>
                            <span class="text-[11px] text-slate-500 {{ request('sort', 'latest') === $sortKey ? '!text-orange-300/80' : '' }}">{{ $sortMeta['hint'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </details>
</aside>

<script>
(function () {
    const panel = document.getElementById('shop-filters');
    if (!panel || !window.matchMedia) return;
    const desktop = window.matchMedia('(min-width: 1024px)');
    const sync = () => {
        if (desktop.matches) panel.setAttribute('open', '');
        else panel.removeAttribute('open');
    };
    sync();
    desktop.addEventListener?.('change', sync);
})();
</script>
