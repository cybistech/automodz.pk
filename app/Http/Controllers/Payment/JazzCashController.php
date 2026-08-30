<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\JazzCashPaymentService;
use Illuminate\Http\Request;

class JazzCashController extends Controller
{
    public function __construct(
        private JazzCashPaymentService $jazzCash,
        private OrderService $orderService,
        private CartService $cart,
    ) {}

    public function return(Request $request)
    {
        $response = $request->all();

        if (! $this->jazzCash->verifyResponse($response)) {
            return redirect()->route('home')->with('error', 'Invalid payment response.');
        }

        $order = Order::where('order_number', $response['pp_TxnRefNo'] ?? '')->firstOrFail();

        if ($this->jazzCash->isSuccessful($response)) {
            $this->orderService->markPaid($order, $response['pp_TxnRefNo'] ?? null, $response);
            $this->cart->clear();
            session(['last_order_id' => $order->id]);

            return redirect()->route('orders.confirmation', [
                'order' => $order,
                'token' => $order->guest_token,
            ])->with('success', 'JazzCash payment successful!');
        }

        $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
        $order->payment?->update(['status' => 'failed', 'gateway_response' => $response]);

        return redirect()->route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ])->with('error', 'JazzCash payment failed. Please try again.');
    }
}
