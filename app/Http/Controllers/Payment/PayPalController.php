<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentGatewayService;
use App\Services\Payments\PayPalPaymentService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function __construct(
        private PayPalPaymentService $paypal,
        private OrderService $orderService,
        private CartService $cart,
        private PaymentGatewayService $gateways,
    ) {}

    public function success(Request $request, Order $order)
    {
        if (! $this->gateways->isCheckoutEnabled('paypal')) {
            abort(404);
        }

        $paypalOrderId = $request->query('token') ?: session('paypal_order_id');

        if (! $paypalOrderId) {
            return redirect()->route('checkout.index')->with('error', 'Missing PayPal payment reference.');
        }

        try {
            $capture = $this->paypal->captureApprovedOrder($order, $paypalOrderId);
        } catch (\Throwable) {
            return redirect()->route('orders.confirmation', [
                'order' => $order,
                'token' => $order->guest_token,
            ])->with('error', 'PayPal payment could not be completed.');
        }

        $captureId = data_get($capture, 'purchase_units.0.payments.captures.0.id');

        $this->orderService->markPaid($order, $captureId, $capture);
        $this->cart->clear();
        session(['last_order_id' => $order->id]);
        session()->forget('paypal_order_id');

        return redirect()->route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ])->with('success', 'PayPal payment successful! Thank you for your order.');
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.index')->with('error', 'PayPal payment was cancelled.');
    }
}
