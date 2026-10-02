@extends('layouts.legal-document')

@section('legal-title', 'Privacy Policy')
@section('legal-meta-description', 'How '.config('site.name').' collects, uses, protects, and shares personal information when you shop at '.config('site.domain').'.')
@section('legal-last-updated', $lastUpdated)

@section('legal-body')
<section>
    <h2>1. Introduction</h2>
    <p>
        {{ config('site.name') }} (“we”, “us”, “our”) respects your privacy and is committed to protecting personal data
        in line with applicable laws in Pakistan and widely recognised data-protection principles.
        This Privacy Policy explains what we collect, why we collect it, how we use and safeguard it,
        and the choices you have when you use {{ config('site.domain') }} (the “Site”), place orders, create an account,
        or communicate with us (collectively, the “Services”).
    </p>
    <p>
        By using the Services, you acknowledge this Policy. If you do not agree, please do not use the Site or submit personal data to us.
    </p>
</section>

<section>
    <h2>2. Data controller</h2>
    <p>
        The data controller for personal information processed through the Site is {{ config('site.name') }},
        reachable at <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
        and {{ config('site.office_city') }}, {{ config('site.office_country') }}.
    </p>
</section>

<section>
    <h2>3. Information we collect</h2>
    <h3>3.1 Information you provide</h3>
    <ul>
        <li><strong>Account data:</strong> name, email address, phone number, password (stored only as a secure one-way hash), and profile details you choose to save.</li>
        <li><strong>Order &amp; checkout data:</strong> billing/shipping name, delivery address, city, phone, email, order notes, and payment method selection.</li>
        <li><strong>Guest checkout:</strong> the same order contact and delivery details when you shop without registering.</li>
        <li><strong>Payment-related data:</strong> we do not store full card numbers on our servers. Payment partners (e.g. Stripe, PayPal, JazzCash, EasyPaisa, or cash-on-delivery workflows) may process transaction references, status, and limited payment metadata according to their own policies.</li>
        <li><strong>Communications:</strong> messages you send by email, WhatsApp, or support channels, including order confirmations and delivery updates if you opt in to WhatsApp automation.</li>
        <li><strong>Reviews &amp; content:</strong> product reviews, ratings, and text you submit on the Site.</li>
    </ul>
    <h3>3.2 Information collected automatically</h3>
    <ul>
        <li><strong>Technical &amp; usage data:</strong> IP address, browser type, device information, pages viewed, referring URLs, and approximate location derived from IP.</li>
        <li><strong>Cookies &amp; similar technologies:</strong> session identifiers, authentication tokens, cart contents, CSRF protection tokens, and preferences needed for security and core Site functionality.</li>
        <li><strong>Security logs:</strong> failed login attempts, admin access, webhook delivery logs (e.g. WhatsApp), and fraud-prevention signals.</li>
    </ul>
    <h3>3.3 Information from third parties</h3>
    <ul>
        <li>Social sign-in providers (if enabled), such as Google or Instagram via SSO, may share name, email, and profile identifier according to your consent on that provider.</li>
        <li>Payment gateways return payment status and transaction identifiers needed to fulfil your order.</li>
        <li>Courier or logistics partners may provide delivery status updates linked to your order.</li>
    </ul>
</section>

<section>
    <h2>4. How we use your information</h2>
    <p>We use personal data only where we have a lawful basis, including to:</p>
    <ul>
        <li>Process, fulfil, ship, and support your orders (contract performance).</li>
        <li>Create and manage your account, authenticate you, and provide order history.</li>
        <li>Send transactional messages (order confirmation, shipping updates, payment status) by email and, where enabled, WhatsApp.</li>
        <li>Generate shipping labels, tracking links, and QR codes that allow you or carriers to view order status through secure tokenised URLs.</li>
        <li>Process payments, prevent fraud, and comply with financial record-keeping obligations.</li>
        <li>Operate, secure, and improve the Site (monitoring, debugging, analytics in aggregated form).</li>
        <li>Respond to inquiries, disputes, and legal requests.</li>
        <li>Send marketing only where you have given clear consent or where permitted by law; you may opt out at any time.</li>
    </ul>
</section>

<section>
    <h2>5. Legal bases (summary)</h2>
    <ul>
        <li><strong>Contract:</strong> processing necessary to sell and deliver products you order.</li>
        <li><strong>Legitimate interests:</strong> site security, fraud prevention, and service improvement, balanced against your rights.</li>
        <li><strong>Consent:</strong> optional marketing, non-essential cookies where required, and WhatsApp messaging where you must confirm orders.</li>
        <li><strong>Legal obligation:</strong> tax, accounting, and regulatory requirements.</li>
    </ul>
</section>

<section>
    <h2>6. Sharing and disclosure</h2>
    <p>We do not sell your personal information. We may share data with:</p>
    <ul>
        <li><strong>Service providers</strong> under confidentiality obligations: hosting, email delivery, payment processors, WhatsApp/Meta Cloud API, analytics (if used), and IT support.</li>
        <li><strong>Delivery partners</strong> to the extent needed to deliver parcels (name, address, phone, tracking reference).</li>
        <li><strong>Professional advisers</strong> (lawyers, accountants) when necessary.</li>
        <li><strong>Authorities</strong> when required by law, court order, or to protect rights, safety, and security.</li>
        <li><strong>Business transfers</strong> in connection with a merger, acquisition, or asset sale, subject to continued protection consistent with this Policy.</li>
    </ul>
    <p>Third-party sites linked from our Site have their own privacy policies; we are not responsible for their practices.</p>
</section>

<section>
    <h2>7. International transfers</h2>
    <p>
        Our primary operations are in Pakistan. Some providers (e.g. cloud hosting, payment, or Meta/WhatsApp infrastructure)
        may process data in other countries. Where data leaves Pakistan, we take steps to ensure appropriate safeguards
        (contractual protections, provider certifications, and minimisation of data shared).
    </p>
</section>

<section>
    <h2>8. Data retention</h2>
    <p>We retain personal data only as long as necessary for the purposes above, including:</p>
    <ul>
        <li>Order and payment records: typically for the period required by tax and commercial law (often several years).</li>
        <li>Account data: until you delete your account or request erasure, subject to legal holds.</li>
        <li>Marketing preferences: until you withdraw consent or unsubscribe.</li>
        <li>Security logs: for a limited period appropriate to investigate incidents.</li>
        <li>Guest tracking tokens: while needed for order tracking links; tokens may be rotated or invalidated when no longer required.</li>
    </ul>
</section>

<section>
    <h2>9. Security measures</h2>
    <p>
        We implement administrative, technical, and organisational measures designed to protect your data, including
        HTTPS encryption in transit, access controls for admin systems, hashed passwords, CSRF protection,
        signed or tokenised guest tracking links, and principle of least privilege for staff access.
        No method of transmission or storage is 100% secure; please use a strong unique password and protect your devices.
    </p>
    <p>
        If you believe your account or order information has been compromised, contact us immediately at
        <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>.
    </p>
</section>

<section>
    <h2>10. Your rights and choices</h2>
    <p>Subject to applicable law, you may have the right to:</p>
    <ul>
        <li>Access personal data we hold about you.</li>
        <li>Correct inaccurate or incomplete data.</li>
        <li>Request deletion of data not required for legal or contractual purposes.</li>
        <li>Object to or restrict certain processing.</li>
        <li>Withdraw consent where processing is consent-based (without affecting prior lawful processing).</li>
        <li>Receive a copy of your data in a portable format where technically feasible.</li>
    </ul>
    <p>
        To exercise these rights, email <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
        from the address associated with your account or order. We may need to verify your identity before responding.
    </p>
</section>

<section>
    <h2>11. Cookies</h2>
    <p>
        Essential cookies and session storage are used for login, cart, checkout, and security.
        You can control cookies through browser settings; disabling essential cookies may prevent you from using parts of the Site.
    </p>
</section>

<section>
    <h2>12. Children</h2>
    <p>
        The Services are not directed at children under 18. We do not knowingly collect personal data from children.
        If you believe a child has provided us data, contact us and we will delete it promptly.
    </p>
</section>

<section>
    <h2>13. WhatsApp and messaging</h2>
    <p>
        If you provide a mobile number and we enable WhatsApp order updates, messages may be sent via Meta’s WhatsApp Business Platform.
        Message content is limited to order-related information. Reply-based confirmations may update order status in our system.
        Meta processes data according to its own terms and privacy policy. You can stop WhatsApp messages by replying STOP where supported
        or by contacting us, though we may still email critical order updates.
    </p>
</section>

<section>
    <h2>14. Changes to this Policy</h2>
    <p>
        We may update this Privacy Policy from time to time. The “Last updated” date at the top will change,
        and material changes may be highlighted on the Site. Continued use after changes constitutes acceptance where permitted by law.
    </p>
</section>

<section>
    <h2>15. Contact</h2>
    <p>
        Privacy inquiries: <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a><br>
        WhatsApp (business): <a href="https://wa.me/{{ config('site.whatsapp') }}">{{ config('site.whatsapp_display') }}</a><br>
        Related: <a href="{{ route('legal.terms') }}">Terms of Use</a>
    </p>
</section>
@endsection
