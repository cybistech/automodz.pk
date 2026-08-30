@extends('layouts.shop')

@section('title', 'My Orders')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-white">My Orders</h1>

    <div class="mt-8 space-y-4">
        @forelse($orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="card block p-5 transition hover:border-orange-500/50">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-white">{{ $order->order_number }}</p>
                        <p class="text-sm text-slate-400">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-orange-400">Rs. {{ number_format($order->total) }}</p>
                        <div class="mt-1 flex gap-2">
                            <span class="badge bg-slate-700 text-slate-300 capitalize">{{ $order->status }}</span>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }} capitalize">{{ $order->payment_status }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="card p-12 text-center text-slate-400">No orders yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</div>
@endsection
