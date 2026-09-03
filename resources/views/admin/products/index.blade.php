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
                                <img src="{{ $product->imageUrl() }}" class="h-10 w-10 rounded object-cover">
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
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-orange-400 hover:text-orange-300">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="ml-3 text-red-400 hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
