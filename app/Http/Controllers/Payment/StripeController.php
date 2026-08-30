<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cart,
    ) {}

    public function success(Request $request, Order $order)
    {
        Stripe::setApiKey(config('payments.stripe.secret'));

        $sessionId = $request->get('session_id');
        $session = Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $this->orderService->markPaid($order, $session->payment_intent, $session->toArray());
            $this->cart->clear();
            session(['last_order_id' => $order->id]);
        }

        return redirect()->route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ])->with('success', 'Payment successful! Thank you for your order.');
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled. You can try again.');
    }
}
