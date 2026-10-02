<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalPaymentService
{
    public function __construct(private PaymentGatewayService $gateways) {}

    public function createCheckoutApprovalUrl(Order $order): string
    {
        $this->gateways->applyRuntimeConfig('paypal');

        $config = config('payments.paypal');
        $baseUrl = ($config['mode'] ?? 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $tokenResponse = Http::asForm()
            ->withBasicAuth($config['client_id'], $config['client_secret'])
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        if (! $tokenResponse->successful()) {
            throw new RuntimeException('Unable to authenticate with PayPal.');
        }

        $accessToken = $tokenResponse->json('access_token');

        $orderResponse = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->order_number,
                    'amount' => [
                        'currency_code' => strtoupper($order->currency),
                        'value' => number_format((float) $order->total, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => route('payment.paypal.success', $order),
                    'cancel_url' => route('payment.paypal.cancel', $order),
                    'brand_name' => config('site.name'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (! $orderResponse->successful()) {
            throw new RuntimeException('Unable to create PayPal checkout order.');
        }

        $approveLink = collect($orderResponse->json('links', []))
            ->firstWhere('rel', 'approve');

        if (! $approveLink || empty($approveLink['href'])) {
            throw new RuntimeException('PayPal approval URL missing.');
        }

        session(['paypal_order_id' => $orderResponse->json('id')]);

        return $approveLink['href'];
    }

    public function captureApprovedOrder(Order $order, string $paypalOrderId): array
    {
        $this->gateways->applyRuntimeConfig('paypal');

        $config = config('payments.paypal');
        $baseUrl = ($config['mode'] ?? 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $tokenResponse = Http::asForm()
            ->withBasicAuth($config['client_id'], $config['client_secret'])
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        $accessToken = $tokenResponse->json('access_token');

        $captureResponse = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture");

        if (! $captureResponse->successful()) {
            throw new RuntimeException('PayPal capture failed.');
        }

        return $captureResponse->json();
    }
}
