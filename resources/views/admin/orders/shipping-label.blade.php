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
            --sl-red: #b91c1c;
            --sl-black: #0a0a0a;
            --sl-line: #1a1a1a;
            --sl-muted: #6b6b6b;
            --sl-row-line: #d8d8d8;
            --sl-font: 'Roboto', Arial, Helvetica, sans-serif;
            --sl-script: 'Dancing Script', 'Segoe Script', cursive;
            --label-w: 644px;
            --label-h: 426px;
            --header-h: 104px;
            --address-h: 116px;
        }

        html, body {
            background: #ebebeb;
            font-family: var(--sl-font);
            color: var(--sl-black);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body.embed-mode {
            background: #fff;
        }

        body.embed-mode .label-wrap {
            padding: 0;
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
        .no-print .btn-download { background: var(--sl-red); color: #fff; }
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
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .sl-header {
            height: var(--header-h);
            max-height: var(--header-h);
            display: grid;
            grid-template-columns: 252px 296px 96px;
            border-bottom: 1px solid var(--sl-line);
            flex-shrink: 0;
            overflow: hidden;
        }

        .sl-header > * {
            height: 100%;
            max-height: var(--header-h);
            min-height: 0;
            overflow: hidden;
        }

        .sl-logo-block {
            border-right: 1px solid var(--sl-line);
            display: flex;
            align-items: flex-start;
            padding: 3px 8px 2px 10px;
        }

        .sl-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            width: 100%;
        }

        .sl-brand-car {
            width: 88px;
            height: 22px;
            display: block;
            margin-bottom: 0;
        }

        .sl-brand-name {
            font-size: 20px;
            font-weight: 900;
            font-style: italic;
            letter-spacing: -0.4px;
            line-height: 0.95;
            text-transform: uppercase;
            color: var(--sl-black);
            white-space: nowrap;
        }

        .sl-brand-name .brand-red {
            color: var(--sl-red);
        }

        .sl-brand-cats {
            margin-top: 2px;
            font-size: 5.8px;
            font-weight: 500;
            letter-spacing: 0.4px;
            line-height: 1.25;
            text-transform: uppercase;
            color: var(--sl-black);
            max-width: 230px;
        }

        .sl-brand-cats .sep {
            margin: 0 3px;
            color: #555;
            font-weight: 400;
        }

        .sl-brand-script {
            margin-top: 1px;
            font-family: var(--sl-script);
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            color: var(--sl-red);
        }

        .sl-order-block {
            padding: 5px 8px 4px;
            border-right: 1px solid var(--sl-line);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-width: 0;
        }

        .sl-order-box {
            width: 100%;
            border: 1px solid var(--sl-line);
            border-radius: 5px;
            padding: 5px 7px 4px;
            box-sizing: border-box;
            background: #fff;
            max-height: calc(var(--header-h) - 10px);
        }

        .sl-order-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 2px;
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
            font-size: 8px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: 0.15px;
            white-space: nowrap;
            line-height: 1.2;
        }

        .sl-tracking-top .lbl {
            display: block;
            margin-bottom: 1px;
        }

        .sl-barcode-small-wrap {
            line-height: 0;
        }

        .sl-barcode-small-wrap svg {
            width: 100%;
            height: 18px;
            max-height: 18px;
            display: block;
        }

        .sl-tracking-code-small {
            display: block;
            font-size: 6.5px;
            font-weight: 500;
            letter-spacing: 0.35px;
            margin-top: 1px;
            text-align: left;
            line-height: 1.1;
        }

        .sl-qr-block {
            padding: 4px 5px 3px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 2px;
        }

        .sl-qr-box,
        .sl-qr-box #qrcode {
            width: 52px;
            height: 52px;
            overflow: hidden;
        }

        .sl-qr-box {
            border: 1px solid #666;
            border-radius: 4px;
            padding: 2px;
            background: #fff;
            line-height: 0;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sl-qr-box #qrcode canvas,
        .sl-qr-box #qrcode img {
            display: block !important;
            width: 48px !important;
            height: 48px !important;
            max-width: 48px !important;
            max-height: 48px !important;
        }

        /* qrcodejs may inject extra img/canvas copies — show only one */
        .sl-qr-box #qrcode canvas ~ canvas,
        .sl-qr-box #qrcode img ~ img {
            display: none !important;
        }

        .sl-qr-caption {
            font-size: 4.8px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.25;
            max-width: 88px;
            color: var(--sl-black);
            flex-shrink: 0;
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
            padding: 8px 10px 7px;
            display: flex;
            min-width: 0;
        }

        .sl-address-col:first-child {
            border-right: 1px solid var(--sl-line);
        }

        .sl-address-panel {
            flex: 1;
            border: 1px solid var(--sl-line);
            border-radius: 6px;
            padding: 6px 8px 5px;
            min-width: 0;
        }

        .sl-pill-heading {
            display: inline-block;
            background: var(--sl-black);
            color: #fff;
            font-size: 7.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            letter-spacing: 0.15px;
            margin-bottom: 5px;
        }

        .sl-address-name {
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 2px;
            line-height: 1.25;
        }

        .sl-address-col.sl-receiver .sl-address-name {
            font-size: 11px;
        }

        .sl-address-lines {
            font-size: 8.5px;
            line-height: 1.35;
            font-weight: 400;
        }

        .sl-contact-row {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
            font-size: 8.5px;
            font-weight: 500;
            line-height: 1.2;
        }

        .sl-contact-row svg {
            width: 10px;
            height: 10px;
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
            padding: 9px 10px 8px;
            display: flex;
            min-width: 0;
        }

        .sl-details-panel {
            flex: 1;
            border: 1px solid var(--sl-line);
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .sl-detail-row {
            flex: 1;
            display: grid;
            grid-template-columns: 34px 1fr auto;
            align-items: center;
            gap: 5px;
            padding: 0 8px;
            border-bottom: 1px solid var(--sl-row-line);
            min-height: 0;
        }

        .sl-detail-row:last-child {
            border-bottom: none;
        }

        .sl-icon-box {
            width: 28px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #efefef;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .sl-icon-box svg {
            width: 17px;
            height: 15px;
            display: block;
            stroke: #1a1a1a;
            fill: none;
        }

        .sl-detail-label {
            font-size: 8.5px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .sl-detail-value {
            font-size: 9px;
            font-weight: 900;
            text-align: right;
            max-width: 132px;
            line-height: 1.15;
            color: var(--sl-black);
        }

        .sl-detail-row:first-child .sl-detail-value {
            font-size: 10px;
            letter-spacing: 0.15px;
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
            padding: 8px 12px 6px;
            border-bottom: 1px solid var(--sl-line);
            min-height: 0;
        }

        .sl-barcode-large-area svg {
            width: 100%;
            max-width: 268px;
            height: 50px;
        }

        .sl-tracking-code-large {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.45px;
            margin-top: 3px;
            line-height: 1;
        }

        .sl-footer {
            height: 44px;
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            align-items: center;
            padding: 3px 10px 4px;
            gap: 8px;
            flex-shrink: 0;
        }

        .sl-footer-left {
            min-width: 0;
        }

        .sl-footer-thanks {
            font-family: var(--sl-script);
            font-size: 10.5px;
            line-height: 1.1;
            color: var(--sl-black);
            margin-bottom: 1px;
            white-space: nowrap;
        }

        .sl-footer-brand-text {
            font-size: 11px;
            font-weight: 900;
            font-style: italic;
            line-height: 1;
            letter-spacing: 0.2px;
            margin-top: 2px;
            position: relative;
            display: inline-block;
            padding-bottom: 2px;
        }

        .sl-footer-brand-text::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--sl-red) 0%, var(--sl-red) 55%, transparent 55%);
            border-radius: 1px;
        }

        .sl-footer-brand-text .brand-red {
            color: var(--sl-red);
        }

        .sl-footer-divider {
            width: 1px;
            height: 28px;
            background: #b0b0b0;
            justify-self: center;
        }

        .sl-footer-tagline {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            min-width: 0;
        }

        .sl-footer-tagline-inner {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
        }

        .sl-footer-tagline-icon {
            width: 18px;
            height: 12px;
            flex-shrink: 0;
        }

        .sl-footer-tagline-text {
            font-size: 5.5px;
            font-weight: 800;
            letter-spacing: 0.35px;
            line-height: 1.35;
            text-transform: uppercase;
            text-align: left;
            color: var(--sl-black);
        }

        @media print {
            @page {
                margin: 0;
            }

            html, body {
                width: var(--label-w);
                height: var(--label-h);
                max-height: var(--label-h);
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                overflow: hidden !important;
            }

            .no-print {
                display: none !important;
            }

            .label-wrap {
                margin: 0 !important;
                padding: 0 !important;
                width: var(--label-w);
                height: var(--label-h);
            }

            .shipping-label {
                width: var(--label-w) !important;
                height: var(--label-h) !important;
                margin: 0 !important;
                border-radius: 0;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
                break-before: avoid-page !important;
                break-after: avoid-page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .sl-qr-box #qrcode {
                overflow: hidden !important;
            }
        }
    </style>
</head>
<body @class(['embed-mode' => request()->boolean('embed')])>
@php
    $isEmbed = request()->boolean('embed');
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

@if (! $isEmbed)
<div class="no-print">
    <button type="button" class="btn-print" id="btn-print">Print shipping label</button>
    <button type="button" class="btn-download" id="btn-download">Download label (PNG)</button>
    <a href="{{ route('admin.orders.show', $order) }}" class="btn-back">← Back to order</a>
</div>
@endif

<div class="label-wrap">
    <article class="shipping-label" aria-label="Shipping label for order {{ $order->order_number }}">
        <header class="sl-header">
            <div class="sl-logo-block">
                <div class="sl-brand" aria-label="AutoModz.pk">
                    <svg class="sl-brand-car" viewBox="0 0 120 32" fill="none" aria-hidden="true">
                        <path d="M8 20 C22 6, 44 4, 62 8 C78 12, 92 10, 108 18" stroke="#0a0a0a" stroke-width="2.6" stroke-linecap="round"/>
                        <path d="M18 22 C34 12, 52 10, 68 14 C84 18, 96 17, 104 22" stroke="#b91c1c" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <p class="sl-brand-name">AUTO<span class="brand-red">MODZ</span>.PK</p>
                    <p class="sl-brand-cats">Auto Parts<span class="sep">|</span> Accessories<span class="sep">|</span> Car Care</p>
                    <p class="sl-brand-script">Drive Your Style</p>
                </div>
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
            <div class="sl-address-col sl-sender">
                <div class="sl-address-panel">
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
            </div>

            <div class="sl-address-col sl-receiver">
                <div class="sl-address-panel">
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
            </div>
        </section>

        <section class="sl-bottom">
            <div class="sl-details-col">
                <div class="sl-details-panel">
                    <div class="sl-detail-row">
                        <div class="sl-icon-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24" stroke-width="1.6">
                                <path d="M4 7h16v12H4z"/>
                                <path d="M8 7V5h8v2"/>
                                <path d="M8 12h8M8 15h5"/>
                            </svg>
                        </div>
                        <span class="sl-detail-label">COD Amount</span>
                        <span class="sl-detail-value">{{ $codDisplay }}</span>
                    </div>
                    <div class="sl-detail-row">
                        <div class="sl-icon-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24" stroke-width="1.6">
                                <path d="M12 3v18"/>
                                <path d="M5 8h14"/>
                                <path d="M7 8l-2 5h4l-1-5zM17 8l-2 5h4l-1-5z"/>
                                <path d="M6 21h12"/>
                            </svg>
                        </div>
                        <span class="sl-detail-label">Parcel Weight</span>
                        <span class="sl-detail-value">{{ $weightDisplay }}</span>
                    </div>
                    <div class="sl-detail-row">
                        <div class="sl-icon-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24" stroke-width="1.6">
                                <path d="M2 9h13v8H2z"/>
                                <path d="M15 11h4l3 3v3h-7v-6z"/>
                                <circle cx="6" cy="18" r="1.5"/>
                                <circle cx="18" cy="18" r="1.5"/>
                            </svg>
                        </div>
                        <span class="sl-detail-label">Payment Mode</span>
                        <span class="sl-detail-value">{{ $paymentLabel }}</span>
                    </div>
                    <div class="sl-detail-row">
                        <div class="sl-icon-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24" stroke-width="1.6">
                                <rect x="4" y="5" width="16" height="15" rx="1"/>
                                <path d="M8 3v4M16 3v4M4 10h16"/>
                                <path d="M8 14h3"/>
                            </svg>
                        </div>
                        <span class="sl-detail-label">Order Date</span>
                        <span class="sl-detail-value">{{ $order->shippingLabelOrderDate() }}</span>
                    </div>
                </div>
            </div>

            <div class="sl-barcode-col">
                <div class="sl-barcode-large-area">
                    <svg id="barcode-bottom"></svg>
                    <p class="sl-tracking-code-large">{{ $tracking }}</p>
                </div>
                <footer class="sl-footer">
                    <div class="sl-footer-left">
                        <p class="sl-footer-thanks">Thank you for shopping with</p>
                        <p class="sl-footer-brand-text">Auto<span class="brand-red">Modz</span>.pk</p>
                    </div>
                    <div class="sl-footer-divider" aria-hidden="true"></div>
                    <div class="sl-footer-tagline">
                        <div class="sl-footer-tagline-inner">
                            <svg class="sl-footer-tagline-icon" viewBox="0 0 36 22" fill="none" aria-hidden="true">
                                <path d="M4 14h3l1.2-3h19.6L29 14h3l1.5 3H2.5L4 14z" stroke="#0a0a0a" stroke-width="1.2" fill="#fff"/>
                                <circle cx="10" cy="17" r="2.2" stroke="#0a0a0a" stroke-width="1.2" fill="#fff"/>
                                <circle cx="26" cy="17" r="2.2" stroke="#0a0a0a" stroke-width="1.2" fill="#fff"/>
                                <path d="M8 11h20" stroke="#b91c1c" stroke-width="1.2"/>
                            </svg>
                            <p class="sl-footer-tagline-text">Quality Products<br>For Your Ride</p>
                        </div>
                    </div>
                </footer>
            </div>
        </section>
    </article>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
(function () {
    var tracking = @json($tracking);
    var qrUrl = @json($qrUrl);
    var orderNumber = @json($order->order_number);
    var labelEl = document.querySelector('.shipping-label');

    function initBarcodes() {
        if (labelEl && labelEl.dataset.barcodesReady === '1') {
            return;
        }

        if (labelEl) {
            labelEl.dataset.barcodesReady = '1';
        }

        JsBarcode('#barcode-top', tracking, {
            format: 'CODE128',
            width: 0.95,
            height: 18,
            displayValue: false,
            margin: 0,
            lineColor: '#0a0a0a',
        });

        JsBarcode('#barcode-bottom', tracking, {
            format: 'CODE128',
            width: 1.35,
            height: 48,
            displayValue: false,
            margin: 0,
            lineColor: '#0a0a0a',
        });
    }

    function initTrackingQr() {
        var host = document.getElementById('qrcode');
        if (! host || host.dataset.qrReady === '1') {
            return;
        }

        host.dataset.qrReady = '1';
        host.innerHTML = '';

        new QRCode(host, {
            text: qrUrl,
            width: 48,
            height: 48,
            colorDark: '#0a0a0a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M,
        });

        // Library sometimes leaves both canvas and img — keep one graphic only
        var canvas = host.querySelector('canvas');
        var images = host.querySelectorAll('img');

        if (canvas && images.length) {
            images.forEach(function (img) {
                img.remove();
            });
        } else if (images.length > 1) {
            for (var i = 1; i < images.length; i += 1) {
                images[i].remove();
            }
        }
    }

    initBarcodes();
    initTrackingQr();

    function getPrintHostDocument() {
        if (window.self !== window.top) {
            try {
                if (window.top.document && window.top.document.body) {
                    return window.top.document;
                }
            } catch (err) {
                /* cross-origin — use current document */
            }
        }

        return document;
    }

    /** Print iframe lives on the admin page, not the embed URL, so browser headers stay off the label URL */
    function createPrintIframe() {
        var hostDoc = getPrintHostDocument();
        var iframe = hostDoc.createElement('iframe');
        iframe.setAttribute('aria-hidden', 'true');
        iframe.setAttribute('title', 'Shipping label print');
        iframe.style.cssText = 'position:fixed;width:0;height:0;border:0;opacity:0;pointer-events:none;left:-9999px;top:0';
        iframe.src = 'about:blank';
        hostDoc.body.appendChild(iframe);
        return iframe;
    }

    function printFromIframe(iframe, onReady) {
        var printWindow = iframe.contentWindow;
        var cleanup = function () {
            if (iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
        };

        printWindow.onafterprint = cleanup;

        onReady(printWindow, function startPrint() {
            printWindow.print();
            setTimeout(cleanup, 1500);
        });
    }

    /** One raster page — avoids Chrome’s extra blank sheet from DOM/iframe height quirks */
    function printLabelAsImage() {
        return waitForLabelImages(labelEl).then(function () {
            return html2canvas(labelEl, {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#ffffff',
                logging: false,
                width: 644,
                height: 426,
            });
        }).then(function (canvas) {
            var dataUrl = canvas.toDataURL('image/png');
            var iframe = createPrintIframe();
            var doc = iframe.contentDocument || iframe.contentWindow.document;

            doc.open();
            doc.write(
                '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title></title><style>'
                + '@page{margin:0;}'
                + 'html,body{margin:0;padding:0;width:644px;height:426px;overflow:hidden;background:#fff;}'
                + 'img{display:block;width:644px;height:426px;margin:0;border:0;}'
                + '</style></head><body>'
                + '<img id="label-print-img" src="' + dataUrl + '" width="644" height="426" alt="">'
                + '</body></html>'
            );
            doc.close();

            printFromIframe(iframe, function (win, startPrint) {
                var img = doc.getElementById('label-print-img');
                if (! img || img.complete) {
                    setTimeout(startPrint, 50);
                    return;
                }
                img.onload = function () { setTimeout(startPrint, 50); };
                img.onerror = function () { setTimeout(startPrint, 50); };
            });
        });
    }

    function printShippingLabel() {
        if (! labelEl || typeof html2canvas !== 'function') {
            return;
        }

        printLabelAsImage();
    }

    function waitForLabelImages(root) {
        var images = root ? root.querySelectorAll('img') : [];

        return Promise.all(Array.prototype.map.call(images, function (img) {
            if (img.complete && img.naturalWidth > 0) {
                return Promise.resolve();
            }

            return new Promise(function (resolve) {
                img.addEventListener('load', resolve, { once: true });
                img.addEventListener('error', resolve, { once: true });
            });
        }));
    }

    function downloadShippingLabel() {
        if (! labelEl || typeof html2canvas !== 'function') {
            window.print();
            return;
        }

        var btn = document.getElementById('btn-download');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Preparing download…';
        }

        waitForLabelImages(labelEl).then(function () {
            return html2canvas(labelEl, {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#ffffff',
                logging: false,
            });
        }).then(function (canvas) {
            var link = document.createElement('a');
            link.download = 'shipping-label-' + orderNumber + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).finally(function () {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Download label (PNG)';
            }
        });
    }

    window.printShippingLabel = printShippingLabel;
    window.downloadShippingLabel = downloadShippingLabel;

    /* Ctrl+P on embed preview must not print the embed URL in browser headers */
    window.print = function () {
        printShippingLabel();
    };

    var printBtn = document.getElementById('btn-print');
    if (printBtn) {
        printBtn.addEventListener('click', printShippingLabel);
    }

    var downloadBtn = document.getElementById('btn-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', downloadShippingLabel);
    }

    /* Auto-print via ?print=1 disabled for now — avoids stealing focus / opening print dialog */
})();
</script>
</body>
</html>
