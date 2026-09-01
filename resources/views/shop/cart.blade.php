@extends('layouts.shop')

@section('title', 'Shopping Cart')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-white">Shopping Cart</h1>

    @if($items->isEmpty())
        <div class="card mt-8 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="mt-4 text-slate-400">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn-primary mt-6 inline-flex">Continue Shopping</a>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    <div class="card flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                        <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg bg-slate-900">
                            @if($item['image'])
                                <img src="{{ asset('storage/'.$item['image']) }}" alt="" width="80" height="80" loading="lazy" decoding="async" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-white">{{ $item['name'] }}</h3>
                            <p class="text-sm text-slate-400">SKU: {{ $item['sku'] }}</p>
                            <p class="mt-1 font-medium text-orange-400">Rs. {{ number_format($item['price']) }}</p>
                        </div>
                        <form action="{{ route('cart.update', $item['product_id']) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" class="input-field w-16 text-center">
                            <button type="submit" class="btn-secondary text-xs">Update</button>
                        </form>
                        <div class="text-right">
                            <p class="font-bold text-white">Rs. {{ number_format($item['total']) }}</p>
                            <form action="{{ route('cart.remove', $item['product_id']) }}" method="POST" class="mt-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-400 hover:text-red-300">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card h-fit p-6">
                <h3 class="font-semibold text-white">Order Summary</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd>Rs. {{ number_format($subtotal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Shipping</dt><dd>{{ $subtotal >= 10000 ? 'Free' : 'Rs. 500' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Tax (5%)</dt><dd>Rs. {{ number_format($subtotal * 0.05) }}</dd></div>
                    <div class="flex justify-between border-t border-slate-700 pt-3 text-lg font-bold">
                        <dt>Total</dt>
                        <dd class="text-orange-400">Rs. {{ number_format($subtotal + ($subtotal >= 10000 ? 0 : 500) + ($subtotal * 0.05)) }}</dd>
                    </div>
                </dl>
                <a href="{{ route('checkout.index') }}" class="btn-primary mt-6 w-full">Proceed to Checkout</a>
                <a href="{{ route('products.index') }}" class="mt-3 block text-center text-sm text-slate-400 hover:text-orange-400">Continue Shopping</a>
            </div>
        </div>
    @endif
</div>
@endsection
