<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping Label — {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@500;600&family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sl-red: #991d2a;
            --sl-black: #0a0a0a;
            --sl-line: #1a1a1a;
            --sl-muted: #6b6b6b;
            --sl-icon-bg: #efefef;
            --sl-font: 'Roboto', Arial, Helvetica, sans-serif;
            --sl-script: 'Dancing Script', 'Segoe Script', cursive;
            --sl-scale: 0.72;
            --label-w: 644px;
            --label-h: 426px;
            --header-h: 96px;
            --address-h: 112px;
        }

        html, body {
            background: #ebebeb;
            font-family: var(--sl-font);
            color: var(--sl-black);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print {
            max-width: calc(var(--label-w) + 40px);
            margin: 16px auto;
            padding: 0 12px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .no-print button,
        .no-print a {
            font-family: var(--sl-font);
            font-size: 14px;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .no-print .btn-print { background: var(--sl-black); color: #fff; }
        .no-print .btn-back { background: #fff; color: var(--sl-black); border: 1px solid #ccc; }

        .label-wrap {
            display: flex;
            justify-content: center;
            padding: 12px;
        }

        .shipping-label {
            width: var(--label-w);
            height: var(--label-h);
            background: #fff;
            border: 1.5px solid var(--sl-line);
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .sl-header {
            height: var(--header-h);
            display: grid;
            grid-template-columns: 252px 296px 96px;
            border-bottom: 1px solid var(--sl-line);
            flex-shrink: 0;
        }

        .sl-logo-block {
            border-right: 1px solid var(--sl-line);
            overflow: hidden;
            position: relative;
        }

        .sl-logo-block img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: left top;
            display: block;
        }

        .sl-order-block {
            padding: 6px 8px 5px;
            border-right: 1px solid var(--sl-line);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sl-order-box {
            width: 100%;
            max-width: 392px;
            border: 1px solid var(--sl-line);
            border-radius: 4px;
            padding: 6px 8px 5px;
        }

        .sl-order-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 4px;
        }

        .sl-order-row .lbl,
        .sl-tracking-top .lbl {
            font-size: 9.5px;
            font-weight: 700;
            color: var(--sl-black);
            white-space: nowrap;
        }

        .sl-order-pill {
            background: var(--sl-black);
            color: #fff;
            font-size: 8.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        .sl-tracking-top .lbl {
            display: block;
            margin-bottom: 2px;
        }

        .sl-barcode-small-wrap svg {
            width: 100%;
            height: 24px;
            display: block;
        }

        .sl-tracking-code-small {
            display: block;
            font-size: 7px;
            font-weight: 500;
            letter-spacing: 0.4px;
            margin-top: 2px;
            text-align: left;
        }

        .sl-qr-block {
            padding: 5px 6px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
        }

        .sl-qr-box {
            border: 1px solid #888;
            border-radius: 3px;
            padding: 3px;
            background: #fff;
        }

        .sl-qr-box canvas,
        .sl-qr-box img {
            display: block !important;
            width: 56px !important;
            height: 56px !important;
        }

        .sl-qr-caption {
            font-size: 5px;
            font-weight: 700;
            letter-spacing: 0.35px;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.35;
            max-width: 96px;
            color: var(--sl-black);
        }

        /* ── Addresses ── */
        .sl-addresses {
            height: var(--address-h);
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid var(--sl-line);
            flex-shrink: 0;
        }

        .sl-address-col {
            padding: 7px 10px 6px;
        }

        .sl-address-col:first-child {
            border-right: 1px solid var(--sl-line);
        }

        .sl-pill-heading {
            display: inline-block;
            background: var(--sl-black);
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 3px;
            letter-spacing: 0.2px;
            margin-bottom: 5px;
        }

        .sl-address-name {
            font-size: 10.5px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .sl-address-lines {
            font-size: 9px;
            line-height: 1.3;
            font-weight: 400;
        }

        .sl-contact-row {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-top: 7px;
            font-size: 11px;
            font-weight: 500;
        }

        .sl-contact-row svg {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
            stroke: var(--sl-black);
        }

        /* ── Bottom ── */
        .sl-bottom {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 0;
        }

        .sl-details-col {
            border-right: 1px solid var(--sl-line);
            display: grid;
            grid-template-rows: repeat(4, 1fr);
        }

        .sl-detail-row {
            display: grid;
            grid-template-columns: 32px 1fr auto;
            align-items: center;
            gap: 6px;
            padding: 0 8px 0 8px;
            border-bottom: 1px solid #dcdcdc;
        }

        .sl-detail-row:last-child {
            border-bottom: none;
        }

        .sl-icon-box {
            width: 30px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sl-icon-box img {
            width: 30px;
            height: 26px;
            object-fit: contain;
            display: block;
        }

        .sl-detail-label {
            font-size: 9px;
            font-weight: 500;
            color: #222;
        }

        .sl-detail-value {
            font-size: 9px;
            font-weight: 900;
            text-align: right;
            max-width: 130px;
            line-height: 1.2;
        }

        .sl-barcode-col {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .sl-barcode-large-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px 10px 4px;
            border-bottom: 1px solid var(--sl-line);
        }

        .sl-barcode-large-area svg {
            width: 100%;
            max-width: 280px;
            height: 52px;
        }

        .sl-tracking-code-large {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .sl-footer {
            height: 42px;
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            align-items: center;
            padding: 0 8px;
            gap: 6px;
            flex-shrink: 0;
        }

        .sl-footer-thanks {
            font-family: var(--sl-script);
            font-size: 11px;
            line-height: 1.05;
            color: var(--sl-black);
            margin-bottom: 0;
        }

        .sl-footer-brand-img {
            height: 16px;
            width: auto;
            display: block;
        }

        .sl-footer-divider {
            width: 1px;
            height: 26px;
            background: #bbb;
        }

        .sl-footer-tagline {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .sl-footer-tagline img {
            height: 26px;
            width: auto;
            display: block;
        }

        @media print {
            @page { size: 644px 426px; margin: 0; }

            html, body { background: #fff; }

            .no-print { display: none !important; }

            .label-wrap { padding: 0; }

            .shipping-label {
                width: var(--label-w);
                height: var(--label-h);
                border-radius: 6px;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
@php
    $tracking = $order->trackingNumber();
    $codAmount = $order->codCollectAmount();
    $weight = $order->parcelWeightKg();
    $weightDisplay = number_format($weight, 2).' kg';
    $codDisplay = 'PKR '.number_format($codAmount, 0);
    $paymentLabel = $order->payment_method === 'cod'
        ? 'Cash on Delivery (COD)'
        : $order->paymentModeLabel();
    $qrUrl = $order->shippingLabelQrUrl();
@endphp

<div class="no-print">
    <button type="button" class="btn-print" onclick="window.print()">Print shipping label</button>
    <a href="{{ route('admin.orders.show', $order) }}" class="btn-back">← Back to order</a>
</div>

<div class="label-wrap">
    <article class="shipping-label" aria-label="Shipping label for order {{ $order->order_number }}">
        <header class="sl-header">
            <div class="sl-logo-block">
                <img src="{{ asset('images/shipping-label-header-left.png') }}" width="350" height="129" alt="AutoModz.pk">
            </div>

            <div class="sl-order-block">
                <div class="sl-order-box">
                    <div class="sl-order-row">
                        <span class="lbl">Order No:</span>
                        <span class="sl-order-pill">{{ $order->order_number }}</span>
                    </div>
                    <div class="sl-tracking-top">
                        <span class="lbl">Tracking No:</span>
                        <div class="sl-barcode-small-wrap">
                            <svg id="barcode-top"></svg>
                            <span class="sl-tracking-code-small">{{ $tracking }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sl-qr-block">
                <div class="sl-qr-box" id="qrcode" role="img" aria-label="QR code for order updates"></div>
                <p class="sl-qr-caption">Scan for order updates</p>
            </div>
        </header>

        <section class="sl-addresses">
            <div class="sl-address-col">
                <span class="sl-pill-heading">SENDER (From)</span>
                <p class="sl-address-name">{{ $sender['name'] }}</p>
                <div class="sl-address-lines">
                    <p>{{ $sender['address_line_1'] }}</p>
                    <p>{{ $sender['address_line_2'] }}</p>
                </div>
                <div class="sl-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $sender['phone'] }}</span>
                </div>
                <div class="sl-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>{{ $sender['email'] }}</span>
                </div>
            </div>

            <div class="sl-address-col">
                <span class="sl-pill-heading">RECEIVER (To)</span>
                <p class="sl-address-name">{{ $order->customer_name }}</p>
                <div class="sl-address-lines">
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ config('shipping.receiver_province', 'Punjab') }}, {{ config('shipping.receiver_country', 'Pakistan') }}</p>
                </div>
                <div class="sl-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $order->customer_phone }}</span>
                </div>
            </div>
        </section>

        <section class="sl-bottom">
            <div class="sl-details-col">
                <div class="sl-detail-row">
                    <div class="sl-icon-box">
                        <img src="{{ asset('images/shipping-label/icon-0.png') }}" width="40" height="34" alt="">
                    </div>
                    <span class="sl-detail-label">COD Amount</span>
                    <span class="sl-detail-value">{{ $codDisplay }}</span>
                </div>
                <div class="sl-detail-row">
                    <div class="sl-icon-box">
                        <img src="{{ asset('images/shipping-label/icon-1.png') }}" width="40" height="34" alt="">
                    </div>
                    <span class="sl-detail-label">Parcel Weight</span>
                    <span class="sl-detail-value">{{ $weightDisplay }}</span>
                </div>
                <div class="sl-detail-row">
                    <div class="sl-icon-box">
                        <img src="{{ asset('images/shipping-label/icon-2.png') }}" width="40" height="34" alt="">
                    </div>
                    <span class="sl-detail-label">Payment Mode</span>
                    <span class="sl-detail-value">{{ $paymentLabel }}</span>
                </div>
                <div class="sl-detail-row">
                    <div class="sl-icon-box">
                        <img src="{{ asset('images/shipping-label/icon-3.png') }}" width="40" height="34" alt="">
                    </div>
                    <span class="sl-detail-label">Order Date</span>
                    <span class="sl-detail-value">{{ $order->shippingLabelOrderDate() }}</span>
                </div>
            </div>

            <div class="sl-barcode-col">
                <div class="sl-barcode-large-area">
                    <svg id="barcode-bottom"></svg>
                    <p class="sl-tracking-code-large">{{ $tracking }}</p>
                </div>
                <footer class="sl-footer">
                    <div>
                        <p class="sl-footer-thanks">Thank you for shopping with</p>
                        <img class="sl-footer-brand-img" src="{{ asset('images/shipping-label/footer-brand.png') }}" width="148" height="22" alt="AutoModz.pk">
                    </div>
                    <div class="sl-footer-divider" aria-hidden="true"></div>
                    <div class="sl-footer-tagline">
                        <img src="{{ asset('images/shipping-label/footer-tagline.png') }}" width="188" height="34" alt="Quality Products For Your Ride">
                    </div>
                </footer>
            </div>
        </section>
    </article>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    var tracking = @json($tracking);
    var qrUrl = @json($qrUrl);
    var autoPrint = new URLSearchParams(window.location.search).get('print') !== '0';

    JsBarcode('#barcode-top', tracking, {
        format: 'CODE128',
        width: 1.05,
        height: 24,
        displayValue: false,
        margin: 0,
        lineColor: '#0a0a0a',
    });

    JsBarcode('#barcode-bottom', tracking, {
        format: 'CODE128',
        width: 1.45,
        height: 52,
        displayValue: false,
        margin: 0,
        lineColor: '#0a0a0a',
    });

    new QRCode(document.getElementById('qrcode'), {
        text: qrUrl,
        width: 56,
        height: 56,
        colorDark: '#0a0a0a',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
    });

    if (autoPrint) {
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 450);
        });
    }
})();
</script>
</body>
</html>
