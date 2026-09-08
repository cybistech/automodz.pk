<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orderService,
        private ShippingService $shipping,
    ) {}

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $shippingCities = $this->shipping->activeCities();

        if ($shippingCities->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Shipping is not available right now. Please try again later.');
        }

        $selectedCityId = (int) old('shipping_city_id', $shippingCities->first()->id);
        $quote = $this->shipping->quote($subtotal, $selectedCityId);

        return view('shop.checkout', [
            'items' => $items,
            'subtotal' => $quote['subtotal'],
            'shipping' => $quote['shipping'],
            'total' => $quote['total'],
            'shippingCities' => $shippingCities,
            'selectedCityId' => $selectedCityId,
            'paymentMethods' => config('payments.methods'),
            'easypaisa' => config('payments.easypaisa'),
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city_id' => 'required|exists:shipping_cities,id',
            'payment_method' => 'required|in:easypaisa,cod',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = $this->orderService->createFromCart($validated);

        return $this->completeOrder($order, $validated['payment_method']);
    }

    private function completeOrder($order, string $method)
    {
        $this->cart->clear();
        session(['last_order_id' => $order->id]);

        if ($method === 'cod') {
            $order->update(['status' => 'confirmed']);
        }

        $message = match ($method) {
            'cod' => 'Order placed successfully. Pay on delivery.',
            'easypaisa' => $this->easypaisaConfirmationMessage($order),
        };

        return redirect()->route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ])->with('success', $message);
    }

    private function easypaisaConfirmationMessage($order): string
    {
        $account = config('payments.easypaisa.account_number');
        $title = config('payments.easypaisa.account_title');

        if ($account) {
            return "Order placed. Send Rs. ".number_format($order->total)." via EasyPaisa to {$title} ({$account}). We will confirm once payment is received.";
        }

        return 'Order placed. Complete your EasyPaisa payment and we will confirm once received.';
    }
}
