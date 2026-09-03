@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-white">Checkout</h1>
        @guest
            <div class="flex items-center gap-3 text-sm">
                <span class="text-slate-400">Checking out as guest</span>
                <a href="{{ route('login', ['redirect' => 'checkout']) }}" class="btn-secondary text-xs">Login</a>
                <a href="{{ route('register') }}" class="text-orange-400 hover:text-orange-300">Register</a>
            </div>
        @else
            <span class="text-sm text-green-400">Signed in as {{ auth()->user()->name }}</span>
        @endguest
    </div>

    @guest
        <div class="mt-4 rounded-lg border border-blue-500/30 bg-blue-500/10 px-4 py-3 text-sm text-blue-200">
            <strong>Guest checkout</strong> — No account needed. You can track your order using your order number and email/phone after placing the order.
            <a href="{{ route('orders.track') }}" class="ml-1 underline">Track order</a>
        </div>
    @endguest

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
                        <select name="shipping_city_id" id="shipping_city_id" required class="input-field mt-1">
                            @foreach($shippingCities as $city)
                                <option
                                    value="{{ $city->id }}"
                                    data-shipping="{{ $city->shippingFee() }}"
                                    @selected($selectedCityId === $city->id)
                                >
                                    {{ $city->name }} — {{ number_format($city->distance_km) }} km — Rs. {{ number_format($city->shippingFee()) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Shipping from {{ config('shipping.origin_city') }} based on city distance.</p>
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
                <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd id="summary-subtotal">Rs. {{ number_format($subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Shipping</dt><dd id="summary-shipping">Rs. {{ number_format($shipping) }}</dd></div>
                <div class="flex justify-between text-lg font-bold"><dt>Total</dt><dd class="text-orange-400" id="summary-total">Rs. {{ number_format($total) }}</dd></div>
            </dl>
            <p class="mt-3 text-xs text-slate-500">No tax applied on items.</p>
            <button type="submit" class="btn-primary mt-6 w-full">
                @guest Place Guest Order @else Place Order @endguest
            </button>
        </div>
    </form>
</div>

<script>
const subtotal = {{ $subtotal }};
const citySelect = document.getElementById('shipping_city_id');
const shippingEl = document.getElementById('summary-shipping');
const totalEl = document.getElementById('summary-total');

function updateShippingSummary() {
    const shipping = parseFloat(citySelect.selectedOptions[0]?.dataset.shipping || 0);
    shippingEl.textContent = 'Rs. ' + shipping.toLocaleString('en-PK');
    totalEl.textContent = 'Rs. ' + (subtotal + shipping).toLocaleString('en-PK');
}

citySelect?.addEventListener('change', updateShippingSummary);

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
