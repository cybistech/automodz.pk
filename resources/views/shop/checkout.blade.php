@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
@php
    $total = $subtotal + $shipping + $tax;
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-white">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="mt-8 grid gap-8 lg:grid-cols-3">
        @csrf
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h2 class="font-semibold text-white">Shipping Information</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-sm text-slate-400">Full Name</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required class="input-field mt-1">
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}" required class="input-field mt-1">
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">Phone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" required class="input-field mt-1" placeholder="+923001234567">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-slate-400">Shipping Address</label>
                        <textarea name="shipping_address" required class="input-field mt-1" rows="2">{{ old('shipping_address', $user?->address) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm text-slate-400">City</label>
                        <input type="text" name="shipping_city" value="{{ old('shipping_city', $user?->city) }}" required class="input-field mt-1">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-slate-400">Order Notes (optional)</label>
                        <textarea name="notes" class="input-field mt-1" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="font-semibold text-white">Payment Method</h2>
                <div class="mt-4 space-y-3">
                    @foreach($paymentMethods as $key => $label)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-700 p-4 transition hover:border-orange-500/50 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-500/10">
                            <input type="radio" name="payment_method" value="{{ $key }}" @checked(old('payment_method', 'cod') === $key) class="text-orange-500 focus:ring-orange-500">
                            <div>
                                <p class="font-medium">{{ $label }}</p>
                                @if($key === 'bank_transfer')
                                    <p class="mt-1 text-xs text-slate-400">{{ $bank['name'] }} - {{ $bank['account_number'] }} ({{ $bank['account_title'] }})</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                <div id="bank-reference" class="mt-4 hidden">
                    <label class="text-sm text-slate-400">Bank Transfer Reference Number</label>
                    <input type="text" name="bank_reference" value="{{ old('bank_reference') }}" class="input-field mt-1" placeholder="Enter your transaction reference">
                </div>
            </div>
        </div>

        <div class="card h-fit p-6">
            <h2 class="font-semibold text-white">Order Summary</h2>
            <div class="mt-4 space-y-3">
                @foreach($items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                        <span>Rs. {{ number_format($item['total']) }}</span>
                    </div>
                @endforeach
            </div>
            <dl class="mt-4 space-y-2 border-t border-slate-700 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd>Rs. {{ number_format($subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Shipping</dt><dd>{{ $shipping > 0 ? 'Rs. '.number_format($shipping) : 'Free' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Tax</dt><dd>Rs. {{ number_format($tax) }}</dd></div>
                <div class="flex justify-between text-lg font-bold"><dt>Total</dt><dd class="text-orange-400">Rs. {{ number_format($total) }}</dd></div>
            </dl>
            <button type="submit" class="btn-primary mt-6 w-full">Place Order</button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const bankRef = document.getElementById('bank-reference');
        bankRef.classList.toggle('hidden', radio.value !== 'bank_transfer' || !radio.checked);
        if (radio.value === 'bank_transfer' && radio.checked) {
            bankRef.querySelector('input').required = true;
        } else {
            bankRef.querySelector('input').required = false;
        }
    });
});
document.querySelector('input[name="payment_method"]:checked')?.dispatchEvent(new Event('change'));
</script>
@endsection
