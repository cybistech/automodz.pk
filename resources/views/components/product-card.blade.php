@props(['product', 'lazy' => true])

@if($product)
<div class="card-hover group overflow-hidden">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative aspect-square overflow-hidden bg-slate-900">
            @if($product->primary_image)
                <img
                    src="{{ $product->imageUrl() }}"
                    alt="{{ $product->name }}"
                    width="400"
                    height="400"
                    @if($lazy) loading="lazy" decoding="async" @else fetchpriority="high" @endif
                    class="h-full w-full object-cover transition group-hover:scale-105"
                >
            @else
                <div class="flex h-full items-center justify-center text-slate-600">
                    <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            @endif
            @if($product->sale_price)
                <span class="badge absolute left-3 top-3 bg-red-500 text-white">
                    @if($product->discount_percent) -{{ $product->discount_percent }}% @else Sale @endif
                </span>
            @endif
            @if($product->is_featured)
                <span class="badge absolute right-3 top-3 bg-orange-500 text-white">Featured</span>
            @endif
        </div>
        <div class="p-4">
            @if($product->brand)
                <p class="text-xs font-medium uppercase tracking-wider text-orange-400">{{ $product->brand }}</p>
            @endif
            <h3 class="mt-1 font-semibold text-white line-clamp-2">{{ $product->name }}</h3>
            <p class="mt-1 text-xs text-slate-400">SKU: {{ $product->sku }}</p>
            <div class="mt-3 flex items-center justify-between">
                <div>
                    @if($product->sale_price)
                        <span class="text-lg font-bold text-orange-400">Rs. {{ number_format($product->sale_price) }}</span>
                        <span class="ml-1 text-sm text-slate-500 line-through">Rs. {{ number_format($product->price) }}</span>
                    @else
                        <span class="text-lg font-bold text-orange-400">Rs. {{ number_format($product->price) }}</span>
                    @endif
                </div>
                @if($product->isInStock())
                    <span class="badge bg-green-500/20 text-green-400">In Stock</span>
                @else
                    <span class="badge bg-red-500/20 text-red-400">Out of Stock</span>
                @endif
            </div>
        </div>
    </a>
</div>
@endif
