@extends('layouts.shop')

@section('title', 'Products')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-8 lg:flex-row">
        <aside class="lg:w-64 flex-shrink-0">
            <div class="card p-5">
                <h3 class="font-semibold text-white">Filters</h3>
                <form method="GET" class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm text-slate-400">Category</label>
                        <select name="category" class="input-field mt-1" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Brand</label>
                        <select name="brand" class="input-field mt-1" onchange="this.form.submit()">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Sort By</label>
                        <select name="sort" class="input-field mt-1" onchange="this.form.submit()">
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
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">
                    @if(request('search'))
                        Results for "{{ request('search') }}"
                    @else
                        All Motorcycle Parts
                    @endif
                </h1>
                <span class="text-sm text-slate-400">{{ $products->total() }} products</span>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
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
