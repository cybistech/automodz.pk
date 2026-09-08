@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="flex gap-2">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products..." class="input-field">
        <button type="submit" class="btn-secondary">Search</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">Add Product</a>
</div>

<div class="card mt-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="border-b border-slate-700 bg-slate-800/50">
            <tr>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">SKU</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Price</th>
                <th class="px-4 py-3 text-left">Stock</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr class="border-b border-slate-800">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($product->primary_image)
                                <img src="{{ $product->imageUrl(null, true) }}" alt="{{ $product->imageAlt() }}" title="{{ $product->imageAlt() }}" class="h-10 w-10 rounded object-cover" width="40" height="40" loading="lazy" decoding="async">
                            @endif
                            <span class="font-medium">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $product->sku }}</td>
                    <td class="px-4 py-3">{{ $product->category->name }}</td>
                    <td class="px-4 py-3">Rs. {{ number_format($product->effective_price) }}</td>
                    <td class="px-4 py-3">{{ $product->stock }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $product->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-700 text-orange-400 transition hover:border-orange-500/50 hover:bg-orange-500/10" title="Edit" aria-label="Edit product">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-700 text-red-400 transition hover:border-red-500/50 hover:bg-red-500/10" title="Delete" aria-label="Delete product">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
