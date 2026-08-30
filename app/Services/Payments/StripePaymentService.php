<?php

namespace App\Services\Payments;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentService
{
    public function createCheckoutSession(Order $order): Session
    {
        Stripe::setApiKey(config('payments.stripe.secret'));

        $lineItems = $order->items->map(fn ($item) => [
            'price_data' => [
                'currency' => strtolower($order->currency === 'PKR' ? 'pkr' : $order->currency),
                'product_data' => [
                    'name' => $item->product_name,
                    'metadata' => ['sku' => $item->product_sku],
                ],
                'unit_amount' => (int) round($item->price * 100),
            ],
            'quantity' => $item->quantity,
        ])->all();

        if ($order->shipping > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->currency === 'PKR' ? 'pkr' : $order->currency),
                    'product_data' => ['name' => 'Shipping'],
                    'unit_amount' => (int) round($order->shipping * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($order->tax > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->currency === 'PKR' ? 'pkr' : $order->currency),
                    'product_data' => ['name' => 'Tax'],
                    'unit_amount' => (int) round($order->tax * 100),
                ],
                'quantity' => 1,
            ];
        }

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.stripe.success', $order).'&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.stripe.cancel', $order),
            'customer_email' => $order->customer_email,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);
    }
}
