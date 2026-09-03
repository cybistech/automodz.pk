<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\JazzCashPaymentService;
use App\Services\Payments\StripePaymentService;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orderService,
        private ShippingService $shipping,
        private StripePaymentService $stripe,
        private JazzCashPaymentService $jazzCash,
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
            'bank' => config('payments.bank'),
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
            'payment_method' => 'required|in:stripe,jazzcash,bank_transfer,cod',
            'notes' => 'nullable|string|max:500',
            'bank_reference' => 'nullable|string|max:100',
        ]);

        if ($validated['payment_method'] === 'bank_transfer' && empty($validated['bank_reference'])) {
            return back()->withInput()->with('error', 'Please provide your bank transfer reference number.');
        }

        $order = $this->orderService->createFromCart($validated);

        return match ($validated['payment_method']) {
            'stripe' => redirect($this->stripe->createCheckoutSession($order)->url),
            'jazzcash' => view('shop.payments.jazzcash-redirect', $this->jazzCash->buildPaymentForm($order)),
            'bank_transfer', 'cod' => $this->completeOfflineOrder($order, $validated['payment_method']),
        };
    }

    private function completeOfflineOrder($order, string $method)
    {
        $this->cart->clear();
        session(['last_order_id' => $order->id]);

        if ($method === 'cod') {
            $order->update(['status' => 'confirmed']);
        }

        return redirect()->route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ])
            ->with('success', $method === 'cod'
                ? 'Order placed successfully. Pay on delivery.'
                : 'Order placed. Please complete your bank transfer and we will confirm your payment.');
    }
}
