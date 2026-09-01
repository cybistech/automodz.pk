@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card p-6"><p class="text-sm text-slate-400">Products</p><p class="mt-2 text-3xl font-bold text-orange-400">{{ $stats['products'] }}</p></div>
    <div class="card p-6"><p class="text-sm text-slate-400">Orders</p><p class="mt-2 text-3xl font-bold text-orange-400">{{ $stats['orders'] }}</p></div>
    <div class="card p-6"><p class="text-sm text-slate-400">Revenue</p><p class="mt-2 text-3xl font-bold text-orange-400">Rs. {{ number_format($stats['revenue']) }}</p></div>
    <div class="card p-6"><p class="text-sm text-slate-400">Customers</p><p class="mt-2 text-3xl font-bold text-orange-400">{{ $stats['customers'] }}</p></div>
</div>

<div class="mt-8 grid gap-8 lg:grid-cols-2">
    <div class="card p-6">
        <h2 class="font-semibold">Recent Orders</h2>
        <div class="mt-4 space-y-3">
            @foreach($recentOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="flex justify-between rounded-lg p-3 hover:bg-slate-800/50">
                    <div>
                        <p class="font-medium">{{ $order->order_number }}</p>
                        <p class="text-xs text-slate-400">{{ $order->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-orange-400">Rs. {{ number_format($order->total) }}</p>
                        <p class="text-xs capitalize text-slate-400">{{ $order->status }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-semibold">Low Stock Alert</h2>
        <div class="mt-4 space-y-3">
            @forelse($lowStock as $product)
                <div class="flex justify-between rounded-lg p-3 hover:bg-slate-800/50">
                    <div>
                        <p class="font-medium">{{ $product->name }}</p>
                        <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                    </div>
                    <span class="badge bg-red-500/20 text-red-400">{{ $product->stock }} left</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">All products are well stocked.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
