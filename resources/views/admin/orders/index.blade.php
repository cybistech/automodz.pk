@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<form method="GET" class="mb-6 flex flex-wrap gap-4">
    <select name="status" class="input-field w-auto" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <select name="payment_status" class="input-field w-auto" onchange="this.form.submit()">
        <option value="">All Payment Status</option>
        @foreach(['pending','paid','failed','refunded'] as $status)
            <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</form>

<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="border-b border-slate-700 bg-slate-800/50">
            <tr>
                <th class="px-4 py-3 text-left">Order</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Payment</th>
                <th class="px-4 py-3 text-left">Total</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr class="border-b border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->customer_name }}</td>
                    <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                    <td class="px-4 py-3">Rs. {{ number_format($order->total) }}</td>
                    <td class="px-4 py-3">
                        <span class="badge bg-slate-700 capitalize">{{ $order->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-400 hover:text-orange-300">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
