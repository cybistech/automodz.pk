<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping Label — {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sl-red: #c8102e;
            --sl-black: #111111;
            --sl-gray: #bdbdbd;
            --sl-gray-light: #e8e8e8;
            --sl-font: 'Roboto', Arial, Helvetica, sans-serif;
            --sl-script: 'Dancing Script', cursive;
        }

        html, body {
            background: #f0f0f0;
            font-family: var(--sl-font);
            color: var(--sl-black);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print {
            max-width: 1060px;
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

        .no-print .btn-print {
            background: var(--sl-black);
            color: #fff;
        }

        .no-print .btn-back {
            background: #fff;
            color: var(--sl-black);
            border: 1px solid #ccc;
        }

        .label-wrap {
            display: flex;
            justify-content: center;
            padding: 12px;
        }

        .shipping-label {
            width: 1040px;
            background: #fff;
            border: 2px solid var(--sl-black);
            border-radius: 10px;
            overflow: hidden;
        }

        /* ── Header ── */
        .sl-header {
            display: grid;
            grid-template-columns: 1fr 340px 130px;
            border-bottom: 1.5px solid var(--sl-gray);
            min-height: 118px;
        }

        .sl-logo-block {
            padding: 14px 20px 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1.5px solid var(--sl-gray);
        }

        .sl-logo-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sl-logo-row svg.car-icon {
            width: 52px;
            height: 36px;
            flex-shrink: 0;
        }

        .sl-brand-text {
            line-height: 1;
        }

        .sl-brand-name {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .sl-brand-name .auto { color: var(--sl-black); }
        .sl-brand-name .modz { color: var(--sl-red); }
        .sl-brand-name .pk { color: var(--sl-black); font-size: 22px; }

        .sl-brand-categories {
            margin-top: 4px;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: var(--sl-black);
        }

        .sl-brand-tagline {
            margin-top: 6px;
            font-family: var(--sl-script);
            font-size: 17px;
            color: var(--sl-black);
            padding-left: 2px;
        }

        .sl-order-block {
            padding: 14px 18px 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
            border-right: 1.5px solid var(--sl-gray);
        }

        .sl-order-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sl-order-row .lbl {
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .sl-order-pill {
            background: var(--sl-black);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            letter-spacing: 0.3px;
        }

        .sl-tracking-top {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sl-tracking-top .lbl {
            font-size: 11px;
            font-weight: 700;
        }

        .sl-barcode-small-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sl-barcode-small-wrap svg {
            max-width: 100%;
            height: 38px;
        }

        .sl-tracking-code-small {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }

        .sl-qr-block {
            padding: 12px 14px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .sl-qr-box {
            border: 1.5px solid var(--sl-gray);
            border-radius: 6px;
            padding: 4px;
            background: #fff;
        }

        .sl-qr-box canvas,
        .sl-qr-box img {
            display: block;
            width: 72px !important;
            height: 72px !important;
        }

        .sl-qr-caption {
            font-size: 6.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.25;
            max-width: 90px;
        }

        /* ── Addresses ── */
        .sl-addresses {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1.5px solid var(--sl-gray);
            min-height: 148px;
        }

        .sl-address-col {
            padding: 14px 18px 16px;
        }

        .sl-address-col:first-child {
            border-right: 1.5px solid var(--sl-gray);
        }

        .sl-pill-heading {
            display: inline-block;
            background: var(--sl-black);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 5px;
            letter-spacing: 0.3px;
            margin-bottom: 10px;
        }

        .sl-address-name {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .sl-address-lines {
            font-size: 11px;
            line-height: 1.55;
            font-weight: 400;
            max-width: 420px;
        }

        .sl-contact-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 11px;
            font-weight: 500;
        }

        .sl-contact-row svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        /* ── Bottom ── */
        .sl-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 210px;
        }

        .sl-details-col {
            border-right: 1.5px solid var(--sl-gray);
            display: flex;
            flex-direction: column;
        }

        .sl-detail-row {
            display: grid;
            grid-template-columns: 28px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 0 16px;
            min-height: 52px;
            border-bottom: 1px solid var(--sl-gray-light);
        }

        .sl-detail-row:last-child {
            border-bottom: none;
        }

        .sl-detail-row svg {
            width: 22px;
            height: 22px;
            justify-self: center;
        }

        .sl-detail-label {
            font-size: 11px;
            font-weight: 500;
            color: #333;
        }

        .sl-detail-value {
            font-size: 12px;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .sl-barcode-col {
            display: flex;
            flex-direction: column;
        }

        .sl-barcode-large-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px 20px 8px;
            border-bottom: 1.5px solid var(--sl-gray);
        }

        .sl-barcode-large-area svg {
            width: 100%;
            max-width: 420px;
            height: 72px;
        }

        .sl-tracking-code-large {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 1px;
            margin-top: 6px;
        }

        .sl-footer {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 10px 16px;
            min-height: 52px;
            gap: 12px;
        }

        .sl-footer-thanks {
            font-family: var(--sl-script);
            font-size: 14px;
            line-height: 1.3;
        }

        .sl-footer-thanks strong {
            font-family: var(--sl-font);
            font-weight: 900;
            font-size: 13px;
            font-style: normal;
        }

        .sl-footer-divider {
            width: 1.5px;
            height: 36px;
            background: var(--sl-gray);
        }

        .sl-footer-tagline {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
        }

        .sl-footer-tagline svg {
            width: 28px;
            height: 18px;
        }

        .sl-footer-tagline span {
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.3;
            max-width: 120px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            html, body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .label-wrap {
                padding: 0;
            }

            .shipping-label {
                width: 100%;
                max-width: 277mm;
                border-width: 1.5px;
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
                <div class="sl-logo-row">
                    <svg class="car-icon" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 32c0-8 6-14 14-16l4-6h20l4 6c8 2 14 8 14 16" stroke="#888" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 32h68" stroke="#888" stroke-width="2" stroke-linecap="round"/>
                        <path d="M14 32a6 6 0 1 1 12 0M54 32a6 6 0 1 1 12 0" stroke="#555" stroke-width="2"/>
                        <path d="M22 16h28" stroke="#c8102e" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M28 20h16" stroke="#888" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <div class="sl-brand-text">
                        <div class="sl-brand-name">
                            <span class="auto">AUTO</span><span class="modz">MODZ</span><span class="pk">.PK</span>
                        </div>
                        <div class="sl-brand-categories">Auto Parts | Accessories | Car Care</div>
                    </div>
                </div>
                <p class="sl-brand-tagline">Drive Your Style</p>
            </div>

            <div class="sl-order-block">
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $sender['phone'] }}</span>
                </div>
                <div class="sl-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $order->customer_phone }}</span>
                </div>
            </div>
        </section>

        <section class="sl-bottom">
            <div class="sl-details-col">
                <div class="sl-detail-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    <span class="sl-detail-label">COD Amount</span>
                    <span class="sl-detail-value">{{ $codDisplay }}</span>
                </div>
                <div class="sl-detail-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v18"/><path d="M5 8h14"/><path d="M6 21h12"/><circle cx="12" cy="8" r="5"/></svg>
                    <span class="sl-detail-label">Parcel Weight</span>
                    <span class="sl-detail-value">{{ $weightDisplay }}</span>
                </div>
                <div class="sl-detail-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <span class="sl-detail-label">Payment Mode</span>
                    <span class="sl-detail-value">{{ $paymentLabel }}</span>
                </div>
                <div class="sl-detail-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
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
                    <p class="sl-footer-thanks">Thank you for shopping with<br><strong>{{ $sender['name'] }}</strong></p>
                    <div class="sl-footer-divider" aria-hidden="true"></div>
                    <div class="sl-footer-tagline">
                        <svg viewBox="0 0 48 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M4 14h6l2-4h8l2 4h6" stroke="#111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="10" cy="16" r="2.5" stroke="#111" stroke-width="1.5"/>
                            <circle cx="30" cy="16" r="2.5" stroke="#111" stroke-width="1.5"/>
                            <path d="M14 10h12" stroke="#c8102e" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span>Quality Products For Your Ride</span>
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
        width: 1.35,
        height: 38,
        displayValue: false,
        margin: 0,
        lineColor: '#111111',
    });

    JsBarcode('#barcode-bottom', tracking, {
        format: 'CODE128',
        width: 2.1,
        height: 72,
        displayValue: false,
        margin: 0,
        lineColor: '#111111',
    });

    new QRCode(document.getElementById('qrcode'), {
        text: qrUrl,
        width: 72,
        height: 72,
        colorDark: '#111111',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
    });

    if (autoPrint) {
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 400);
        });
    }
})();
</script>
</body>
</html>
