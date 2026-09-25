@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="card p-6">
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
                @if($order->tax > 0)
                    <div class="flex justify-between"><dt class="text-slate-400">Tax</dt><dd>Rs. {{ number_format($order->tax) }}</dd></div>
                @endif
                <div class="flex justify-between font-bold"><dt>Total</dt><dd class="text-orange-400">Rs. {{ number_format($order->total) }}</dd></div>
            </dl>
        </div>

        <div class="card p-6">
            <h3 class="font-semibold">Shipping label</h3>
            <p class="mt-2 text-sm text-slate-400">Preview the courier label below. Print or download when ready.</p>
            <p class="mt-1 text-xs text-slate-500">In the print dialog, turn off <span class="text-slate-400">Headers and footers</span> so no page URL appears on the label.</p>
            <div class="mt-4 overflow-x-auto overflow-y-hidden rounded-lg border border-slate-700 bg-[#ebebeb]">
                <iframe
                    id="shipping-label-preview"
                    src="{{ route('admin.orders.shipping-label', ['order' => $order, 'embed' => 1]) }}"
                    class="mx-auto block w-full max-w-[680px] border-0"
                    style="height: 460px;"
                    title="Shipping label preview for order {{ $order->order_number }}"
                    loading="lazy"
                    tabindex="-1"
                ></iframe>
            </div>
            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:max-w-xl">
                <button type="button" id="shipping-label-print" class="btn-primary flex flex-1 items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 14h12v8H6z"/></svg>
                    Print label
                </button>
                <button type="button" id="shipping-label-download" class="flex flex-1 items-center justify-center gap-2 rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-100 transition hover:border-orange-500/50 hover:bg-slate-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4L8 11M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
                    Download PNG
                </button>
            </div>
            <a
                href="{{ route('admin.orders.shipping-label', $order) }}"
                target="_blank"
                rel="noopener"
                class="mt-3 inline-block text-sm text-orange-400 hover:text-orange-300"
            >
                Open full label view
            </a>
        </div>
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

@push('scripts')
<script>
    (function () {
        var frame = document.getElementById('shipping-label-preview');
        if (! frame) {
            return;
        }

        function whenFrameReady(callback) {
            if (frame.contentWindow && frame.contentWindow.printShippingLabel) {
                callback(frame.contentWindow);
                return;
            }

            frame.addEventListener('load', function onLoad() {
                frame.removeEventListener('load', onLoad);
                callback(frame.contentWindow);
            });
        }

        var printBtn = document.getElementById('shipping-label-print');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                whenFrameReady(function (win) {
                    if (typeof win.printShippingLabel === 'function') {
                        win.printShippingLabel();
                    }
                });
            });
        }

        var downloadBtn = document.getElementById('shipping-label-download');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                whenFrameReady(function (win) {
                    if (typeof win.downloadShippingLabel === 'function') {
                        win.downloadShippingLabel();
                    }
                });
            });
        }
    })();
</script>
@endpush
@endsection
