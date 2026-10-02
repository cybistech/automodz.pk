@extends('layouts.legal-document')

@section('legal-title', 'Terms of Use')
@section('legal-meta-description', 'Terms and conditions for using '.config('site.name').', placing orders, payments, delivery, and returns at '.config('site.domain').'.')
@section('legal-last-updated', $lastUpdated)

@section('legal-body')
<section>
    <h2>1. Agreement to terms</h2>
    <p>
        These Terms of Use (“Terms”) govern access to and use of {{ config('site.domain') }} and related services operated by
        {{ config('site.name') }} (“we”, “us”, “our”). By accessing the Site, creating an account, or placing an order,
        you agree to these Terms and our <a href="{{ route('legal.privacy') }}">Privacy Policy</a>.
        If you do not agree, do not use the Site.
    </p>
</section>

<section>
    <h2>2. Eligibility</h2>
    <p>
        You must be at least 18 years old and capable of entering a binding contract under applicable law in Pakistan.
        You represent that information you provide is accurate, current, and complete, and that you will update it as needed.
    </p>
</section>

<section>
    <h2>3. Account registration and security</h2>
    <ul>
        <li>You are responsible for maintaining the confidentiality of your login credentials and for all activity under your account.</li>
        <li>Notify us immediately at <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a> if you suspect unauthorised access.</li>
        <li>We may suspend or terminate accounts that violate these Terms, engage in fraud, or abuse the Site or staff.</li>
        <li>Social sign-in (SSO) is subject to the third-party provider’s terms in addition to ours.</li>
    </ul>
</section>

<section>
    <h2>4. Products, pricing, and availability</h2>
    <ul>
        <li>Product descriptions, images, specifications, and compatibility information are provided in good faith but may contain errors. We reserve the right to correct mistakes and cancel orders affected by material errors in price or listing.</li>
        <li>Prices are shown in Pakistani Rupees (PKR) unless stated otherwise and may change without notice until an order is confirmed.</li>
        <li>Stock availability is not guaranteed until checkout is complete and we accept the order.</li>
        <li>Modifications and performance parts may require professional installation; improper installation or use is at your own risk.</li>
    </ul>
</section>

<section>
    <h2>5. Orders and contract formation</h2>
    <ul>
        <li>Submitting an order is an offer to purchase. A binding contract is formed when we confirm the order (email, on-screen confirmation, or WhatsApp confirmation where enabled).</li>
        <li>We may refuse or cancel orders (e.g. suspected fraud, stock unavailability, pricing error, or failure to verify payment).</li>
        <li>Guest orders are supported; you must retain your order number and secure tracking link or contact details to track or support the order.</li>
        <li>Order status updates (pending, confirmed, processing, shipped, delivered, cancelled) are shown in your account or via authorised tracking links.</li>
    </ul>
</section>

<section>
    <h2>6. Payment terms</h2>
    <ul>
        <li>Accepted methods may include cash on delivery (COD), EasyPaisa, JazzCash, Stripe, PayPal, and other methods displayed at checkout.</li>
        <li>COD orders may require phone verification; refusal or repeated failed delivery attempts may affect future COD eligibility.</li>
        <li>For bank/wallet transfers, you must follow instructions exactly and provide any reference we request. We are not liable for delays caused by incorrect references or third-party processing times.</li>
        <li>Online card/wallet payments are processed by licensed third-party gateways; you also agree to their terms when using those methods.</li>
        <li>Title and risk of loss pass to you upon delivery to the address you provide, subject to courier terms and applicable consumer law.</li>
    </ul>
</section>

<section>
    <h2>7. Shipping and delivery</h2>
    <ul>
        <li>Delivery times are estimates only and depend on location, courier capacity, weather, and holidays within Pakistan.</li>
        <li>You must provide a complete, accurate delivery address and reachable phone number. Additional fees may apply for remote areas or re-delivery.</li>
        <li>Inspect parcels on delivery; report visible damage or missing items within 48 hours with photos where possible.</li>
        <li>Tracking numbers, labels, and QR codes are provided for convenience; carriers may use their own tracking systems.</li>
    </ul>
</section>

<section>
    <h2>8. Returns, refunds, and cancellations</h2>
    <ul>
        <li>Change-of-mind returns may be accepted for unused, resaleable items in original packaging within a reasonable period (typically 7 days of delivery) unless the product is marked non-returnable (e.g. electrical items once installed, custom items, or hygiene-sensitive goods).</li>
        <li>Defective or wrong items: contact us promptly with order details and evidence; we will arrange replacement, repair, or refund as appropriate.</li>
        <li>Refunds are processed to the original payment method where possible; COD refunds may be issued via bank transfer or wallet at our discretion.</li>
        <li>Shipping costs on returns may be non-refundable except where the error is ours or the product is defective.</li>
        <li>You may request cancellation before shipment; once dispatched, cancellation may not be possible and return procedures apply.</li>
    </ul>
    <p>For return requests, email <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a> with your order number.</p>
</section>

<section>
    <h2>9. Acceptable use</h2>
    <p>You agree not to:</p>
    <ul>
        <li>Use the Site for unlawful purposes or violate any applicable law or regulation.</li>
        <li>Attempt unauthorised access to systems, accounts, admin areas, or other users’ data.</li>
        <li>Scrape, overload, or interfere with Site operation; bypass security or rate limits.</li>
        <li>Submit false orders, fraudulent chargebacks, or abusive content in reviews.</li>
        <li>Resell products in violation of manufacturer restrictions or our policies.</li>
        <li>Misuse tracking tokens, QR links, or WhatsApp automation to access others’ orders.</li>
    </ul>
</section>

<section>
    <h2>10. Intellectual property</h2>
    <p>
        The Site, logos, design, text, graphics, and software are owned by or licensed to {{ config('site.name') }} and protected by intellectual property laws.
        You may not copy, modify, distribute, or create derivative works without written permission.
        Product trademarks belong to their respective owners.
    </p>
</section>

<section>
    <h2>11. User content</h2>
    <p>
        Reviews and submissions you post grant us a non-exclusive, royalty-free licence to display and moderate that content on the Site.
        You warrant that your content is honest, lawful, and does not infringe third-party rights. We may remove content that violates these Terms.
    </p>
</section>

<section>
    <h2>12. Disclaimers</h2>
    <p>
        THE SITE AND PRODUCTS ARE PROVIDED “AS IS” AND “AS AVAILABLE” TO THE MAXIMUM EXTENT PERMITTED BY LAW.
        WE DISCLAIM WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT EXCEPT WHERE SUCH DISCLAIMERS ARE NOT ALLOWED.
        Vehicle modifications may affect safety, warranty, and legal compliance; you are responsible for ensuring parts meet local regulations and manufacturer requirements.
    </p>
</section>

<section>
    <h2>13. Limitation of liability</h2>
    <p>
        TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, {{ strtoupper(config('site.name')) }} AND ITS OFFICERS, EMPLOYEES, AND SUPPLIERS SHALL NOT BE LIABLE FOR
        INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR LOSS OF PROFITS, DATA, OR GOODWILL,
        ARISING FROM YOUR USE OF THE SITE OR PRODUCTS, EVEN IF ADVISED OF THE POSSIBILITY.
        OUR TOTAL LIABILITY FOR ANY CLAIM RELATING TO AN ORDER SHALL NOT EXCEED THE AMOUNT YOU PAID FOR THAT ORDER,
        EXCEPT WHERE LIABILITY CANNOT BE LIMITED BY LAW (INCLUDING STATUTORY CONSUMER RIGHTS IN PAKISTAN WHERE APPLICABLE).
    </p>
</section>

<section>
    <h2>14. Indemnity</h2>
    <p>
        You agree to indemnify and hold us harmless from claims, losses, and expenses (including reasonable legal fees)
        arising from your breach of these Terms, misuse of the Site, or violation of any law or third-party rights.
    </p>
</section>

<section>
    <h2>15. Force majeure</h2>
    <p>
        We are not liable for failure or delay due to events beyond reasonable control, including natural disasters,
        strikes, government actions, network outages, or courier disruptions.
    </p>
</section>

<section>
    <h2>16. Governing law and disputes</h2>
    <p>
        These Terms are governed by the laws of Pakistan. Courts in {{ config('site.office_city') }} shall have non-exclusive jurisdiction,
        without prejudice to mandatory consumer protections. We encourage you to contact us first to resolve disputes amicably.
    </p>
</section>

<section>
    <h2>17. Changes to terms</h2>
    <p>
        We may revise these Terms at any time. Updated Terms will be posted on this page with a new “Last updated” date.
        Material changes may be notified on the Site. Continued use after changes constitutes acceptance where permitted by law.
    </p>
</section>

<section>
    <h2>18. Contact</h2>
    <p>
        {{ config('site.name') }}<br>
        Email: <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a><br>
        WhatsApp: <a href="https://wa.me/{{ config('site.whatsapp') }}">{{ config('site.whatsapp_display') }}</a><br>
        {{ config('site.office_city') }}, {{ config('site.office_country') }}<br>
        <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
    </p>
</section>
@endsection
