@extends('layouts.shop')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Order {{ $order->order_number }}</h1>
                <p class="text-sm text-slate-400">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <span class="badge bg-slate-700 capitalize">{{ $order->status }}</span>
                <span class="badge {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }} capitalize">{{ $order->payment_status }}</span>
            </div>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2">
            <div>
                <h3 class="font-semibold text-white">Shipping Details</h3>
                <p class="mt-2 text-sm text-slate-300">{{ $order->customer_name }}</p>
                <p class="text-sm text-slate-400">{{ $order->customer_email }}</p>
                <p class="text-sm text-slate-400">{{ $order->customer_phone }}</p>
                <p class="mt-2 text-sm text-slate-300">{{ $order->shipping_address }}</p>
                <p class="text-sm text-slate-400">{{ $order->shipping_city }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-white">Payment</h3>
                <p class="mt-2 text-sm text-slate-300 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                @if($order->bank_reference)
                    <p class="text-sm text-slate-400">Ref: {{ $order->bank_reference }}</p>
                @endif
            </div>
        </div>

        <div class="mt-8">
            <h3 class="font-semibold text-white">Items</h3>
            <div class="mt-4 space-y-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between border-b border-slate-800 py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $item->product_name }}</p>
                            <p class="text-slate-400">SKU: {{ $item->product_sku }} × {{ $item->quantity }}</p>
                        </div>
                        <p class="font-medium">Rs. {{ number_format($item->total) }}</p>
                    </div>
                @endforeach
            </div>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd>Rs. {{ number_format($order->subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Shipping</dt><dd>Rs. {{ number_format($order->shipping) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Tax</dt><dd>Rs. {{ number_format($order->tax) }}</dd></div>
                <div class="flex justify-between text-lg font-bold"><dt>Total</dt><dd class="text-orange-400">Rs. {{ number_format($order->total) }}</dd></div>
            </dl>
        </div>

        @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
            <div class="mt-6 rounded-lg border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-200">
                Please transfer Rs. {{ number_format($order->total) }} to our bank account. Your order will be processed once payment is confirmed.
            </div>
        @endif
    </div>
</div>
@endsection
