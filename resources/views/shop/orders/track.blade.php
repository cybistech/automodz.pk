@extends('layouts.shop')

@section('title', 'Track Order')

@section('content')
<div class="mx-auto max-w-lg px-4 py-12 sm:px-6 lg:px-8">
    <div class="card p-8">
        <h1 class="text-2xl font-bold text-white">Track Your Order</h1>
        <p class="mt-2 text-sm text-slate-400">Enter your order number and the email or phone used at checkout.</p>

        <form action="{{ route('orders.track.submit') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="text-sm text-slate-400">Order Number</label>
                <input type="text" name="order_number" value="{{ old('order_number') }}" required class="input-field mt-1 uppercase" placeholder="AP-XXXXXXXX">
            </div>
            <div>
                <label class="text-sm text-slate-400">Email or Phone</label>
                <input type="text" name="contact" value="{{ old('contact') }}" required class="input-field mt-1" placeholder="you@email.com or 03001234567">
            </div>
            <button type="submit" class="btn-primary w-full">Track Order</button>
        </form>

        <div class="mt-6 border-t border-slate-700 pt-6 text-center text-sm text-slate-400">
            Have an account? <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-300">Sign in</a> to see all orders.
        </div>
    </div>
</div>
@endsection
