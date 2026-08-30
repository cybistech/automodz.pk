<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private CartService $cart) {}

    public function createFromCart(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $items = $this->cart->items();

            if ($items->isEmpty()) {
                throw new \RuntimeException('Your cart is empty.');
            }

            $subtotal = $this->cart->subtotal();
            $shipping = $subtotal >= 10000 ? 0 : 500;
            $tax = round($subtotal * 0.05, 2);
            $total = $subtotal + $shipping + $tax;

            $order = Order::create([
                'order_number' => 'AP-'.strtoupper(Str::random(8)),
                'guest_token' => Str::random(48),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'currency' => config('payments.currency', 'PKR'),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'notes' => $data['notes'] ?? null,
                'bank_reference' => $data['bank_reference'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);

                Product::whereKey($item['product_id'])->decrement('stock', $item['quantity']);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $data['payment_method'],
                'status' => 'pending',
                'amount' => $total,
                'currency' => $order->currency,
            ]);

            return $order->load('items', 'payment');
        });
    }

    public function markPaid(Order $order, ?string $transactionId = null, ?array $gatewayResponse = null): void
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $order->payment?->update([
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now(),
        ]);
    }
}
