@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 card p-6">
        <h2 class="font-semibold">Order Details</h2>
        <div class="mt-4 space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between border-b border-slate-800 py-3">
                    <div>
                        <p class="font-medium">{{ $item->product_name }}</p>
                        <p class="text-xs text-slate-400">{{ $item->product_sku }} × {{ $item->quantity }}</p>
                    </div>
                    <p>Rs. {{ number_format($item->total) }}</p>
                </div>
            @endforeach
        </div>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd>Rs. {{ number_format($order->subtotal) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Shipping</dt><dd>Rs. {{ number_format($order->shipping) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Tax</dt><dd>Rs. {{ number_format($order->tax) }}</dd></div>
            <div class="flex justify-between font-bold"><dt>Total</dt><dd class="text-orange-400">Rs. {{ number_format($order->total) }}</dd></div>
        </dl>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="font-semibold">Customer</h3>
            <p class="mt-2 text-sm">{{ $order->customer_name }}</p>
            <p class="text-sm text-slate-400">{{ $order->customer_email }}</p>
            <p class="text-sm text-slate-400">{{ $order->customer_phone }}</p>
            <p class="mt-2 text-sm">{{ $order->shipping_address }}</p>
            <p class="text-sm text-slate-400">{{ $order->shipping_city }}</p>
        </div>

        <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="card p-6">
            @csrf @method('PATCH')
            <h3 class="font-semibold">Update Status</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-sm text-slate-400">Order Status</label>
                    <select name="status" class="input-field mt-1">
                        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-400">Payment Status</label>
                    <select name="payment_status" class="input-field mt-1">
                        @foreach(['pending','paid','failed','refunded'] as $status)
                            <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary w-full">Update Order</button>
            </div>
        </form>
    </div>
</div>
@endsection
