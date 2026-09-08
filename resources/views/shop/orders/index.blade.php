@extends('layouts.account')

@section('title', 'My Orders')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-slate-400">Track purchases, payment, and delivery progress.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary text-sm">Continue shopping</a>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <a
            href="{{ route('orders.index') }}"
            class="rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ ! request('status') ? 'border-orange-500/40 bg-orange-500/15 text-orange-300' : 'border-slate-700 text-slate-400 hover:border-slate-500 hover:text-slate-200' }}"
        >
            All ({{ $statusCounts->sum() }})
        </a>
        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $status)
            @php
                $count = (int) ($statusCounts[$status] ?? 0);
                if ($count === 0 && request('status') !== $status) {
                    continue;
                }
                $sample = new \App\Models\Order(['status' => $status]);
            @endphp
            <a
                href="{{ route('orders.index', ['status' => $status]) }}"
                class="rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ request('status') === $status ? 'border-orange-500/40 bg-orange-500/15 text-orange-300' : 'border-slate-700 text-slate-400 hover:border-slate-500 hover:text-slate-200' }}"
            >
                {{ $sample->statusLabel() }} ({{ $count }})
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse($orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="card-hover group block p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-white group-hover:text-orange-300">{{ $order->order_number }}</p>
                            <x-order-status-badge :order="$order" />
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $order->paymentBadgeClass() }}">
                                {{ $order->paymentStatusLabel() }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-slate-400">
                            {{ $order->created_at->format('M d, Y · h:i A') }}
                            · {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}
                            · {{ config('payments.methods')[$order->payment_method] ?? ucwords(str_replace('_', ' ', $order->payment_method)) }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">{{ $order->shipping_city }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-display text-xl font-bold text-orange-400">Rs. {{ number_format($order->total) }}</p>
                        <p class="mt-2 text-xs font-semibold text-slate-500 transition group-hover:text-orange-400">View details →</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="card p-12 text-center">
                <p class="text-slate-400">
                    @if(request('status'))
                        No {{ request('status') }} orders found.
                    @else
                        You have not placed any orders yet.
                    @endif
                </p>
                <a href="{{ route('products.index') }}" class="btn-primary mt-6 inline-flex">Browse products</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</div>
@endsection
