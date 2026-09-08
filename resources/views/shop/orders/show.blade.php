@extends(auth()->check() && empty($isGuestConfirmation) ? 'layouts.account' : 'layouts.shop')

@section('title', 'Order '.$order->order_number)
@section('meta_title', 'Order '.$order->order_number.' | '.config('site.name'))
@section('meta_description', 'Order details for '.$order->order_number.' on '.config('site.domain').'.')
@section('meta_image', \App\Support\Seo::defaultOgImage())
@section('meta_image_alt', config('site.name').' — '.config('site.tagline'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="{{ auth()->check() && empty($isGuestConfirmation) ? 'mx-auto max-w-5xl' : 'mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8' }}">
    @if(auth()->check() && empty($isGuestConfirmation))
        <a href="{{ route('orders.index') }}" class="mb-4 inline-flex text-sm font-semibold text-slate-400 transition hover:text-orange-400">← Back to orders</a>
    @endif

    @if(!empty($isGuestConfirmation))
        <div class="mb-6 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-300">
            Your guest order was placed successfully! Save your order number <strong>{{ $order->order_number }}</strong> to track it later.
            <a href="{{ route('orders.track') }}" class="ml-1 underline">Track order</a> ·
            <a href="{{ route('register') }}" class="underline">Create account</a> to link this order.
        </div>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-white sm:text-3xl">Order {{ $order->order_number }}</h1>
            <p class="mt-1 text-sm text-slate-400">Placed on {{ $order->created_at->format('M d, Y · h:i A') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-order-status-badge :order="$order" />
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $order->paymentBadgeClass() }}">
                {{ $order->paymentStatusLabel() }}
            </span>
        </div>
    </div>

    <div class="mt-6">
        <x-order-status-timeline :order="$order" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <h2 class="font-semibold text-white">Items</h2>
            <div class="mt-4 space-y-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between gap-4 border-b border-slate-800 py-3 text-sm last:border-0">
                        <div>
                            <p class="font-medium text-white">{{ $item->product_name }}</p>
                            <p class="text-slate-400">SKU: {{ $item->product_sku }} · Qty {{ $item->quantity }}</p>
                            <p class="text-slate-500">Rs. {{ number_format($item->price) }} each</p>
                        </div>
                        <p class="shrink-0 font-semibold text-slate-200">Rs. {{ number_format($item->total) }}</p>
                    </div>
                @endforeach
            </div>
            <dl class="mt-4 space-y-2 border-t border-slate-800 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd>Rs. {{ number_format($order->subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Shipping</dt><dd>Rs. {{ number_format($order->shipping) }}</dd></div>
                @if($order->tax > 0)
                    <div class="flex justify-between"><dt class="text-slate-400">Tax</dt><dd>Rs. {{ number_format($order->tax) }}</dd></div>
                @endif
                <div class="flex justify-between text-lg font-bold"><dt>Total</dt><dd class="text-orange-400">Rs. {{ number_format($order->total) }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="font-semibold text-white">Shipping</h3>
                <p class="mt-3 text-sm text-slate-200">{{ $order->customer_name }}</p>
                <p class="text-sm text-slate-400">{{ $order->customer_email }}</p>
                <p class="text-sm text-slate-400">{{ $order->customer_phone }}</p>
                <p class="mt-3 text-sm text-slate-300">{{ $order->shipping_address }}</p>
                <p class="text-sm text-slate-400">{{ $order->shipping_city }}</p>
            </div>

            <div class="card p-6">
                <h3 class="font-semibold text-white">Payment</h3>
                <p class="mt-3 text-sm text-slate-200">{{ config('payments.methods')[$order->payment_method] ?? ucwords(str_replace('_', ' ', $order->payment_method)) }}</p>
                <p class="mt-1 text-sm {{ str_contains($order->paymentBadgeClass(), 'green') ? 'text-green-300' : 'text-yellow-300' }}">{{ $order->paymentStatusLabel() }}</p>
                @if($order->bank_reference)
                    <p class="mt-2 text-sm text-slate-400">Ref: {{ $order->bank_reference }}</p>
                @endif
                @if($order->payment?->transaction_id)
                    <p class="mt-2 text-xs text-slate-500">Txn: {{ $order->payment->transaction_id }}</p>
                @endif
            </div>

            @if($order->notes)
                <div class="card p-6">
                    <h3 class="font-semibold text-white">Notes</h3>
                    <p class="mt-3 text-sm text-slate-300">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($order->payment_method === 'easypaisa' && $order->payment_status === 'pending')
        <div class="mt-6 rounded-xl border border-yellow-500/30 bg-yellow-500/10 p-4 text-sm text-yellow-200">
            @if(config('payments.easypaisa.account_number'))
                Please send <strong>Rs. {{ number_format($order->total) }}</strong> via EasyPaisa to
                <strong>{{ config('payments.easypaisa.account_title') }}</strong>
                ({{ config('payments.easypaisa.account_number') }}).
            @else
                Please complete your EasyPaisa payment of Rs. {{ number_format($order->total) }}.
            @endif
            Your order will be processed once payment is confirmed.
        </div>
    @endif
</div>
@endsection
