@extends('layouts.shop')

@section('title', 'Track '.$order->displayTrackingNumber())
@section('meta_title', 'Track order '.$order->order_number.' | '.config('site.name'))
@section('meta_description', 'Live tracking for order '.$order->order_number.' on '.config('site.domain').'.')
@section('meta_image', \App\Support\Seo::defaultOgImage())
@section('meta_image_alt', config('site.name').' — '.config('site.tagline'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <p class="text-sm font-semibold uppercase tracking-wide text-orange-400">Shipment tracking</p>
    <h1 class="mt-2 font-display text-2xl font-bold text-white sm:text-3xl">Order {{ $order->order_number }}</h1>
    <p class="mt-1 text-sm text-slate-400">Updated {{ $order->updated_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }} (PKT)</p>

    <div class="mt-6 card p-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tracking ID</p>
        <p class="mt-2 break-all font-mono text-xl font-bold tracking-wide text-white sm:text-2xl">{{ $order->displayTrackingNumber() }}</p>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <x-order-status-badge :order="$order" />
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $order->paymentBadgeClass() }}">
                {{ $order->paymentStatusLabel() }}
            </span>
        </div>
        <p class="mt-3 text-sm text-slate-300">
            Current status: <strong class="text-white">{{ $order->statusLabel() }}</strong>
        </p>
    </div>

    <div class="mt-6">
        <x-order-status-timeline :order="$order" />
    </div>

    <div class="mt-6 card p-6 text-sm text-slate-300">
        <h2 class="font-semibold text-white">Delivery details</h2>
        <p class="mt-3 text-slate-200">{{ $order->customer_name }}</p>
        <p class="mt-2">{{ $order->shipping_address }}</p>
        <p class="text-slate-400">{{ $order->shipping_city }}</p>
    </div>

    <p class="mt-8 text-center text-sm text-slate-500">
        Questions? <a href="{{ route('home') }}" class="text-orange-400 hover:text-orange-300">{{ config('site.name') }}</a>
        @if(config('site.whatsapp'))
            · <a href="https://wa.me/{{ config('site.whatsapp') }}" class="text-orange-400 hover:text-orange-300" target="_blank" rel="noopener">WhatsApp support</a>
        @endif
    </p>
</div>
@endsection
