<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentGatewayService;
use App\Services\Payments\JazzCashPaymentService;
use App\Services\Payments\PayPalPaymentService;
use App\Services\Payments\StripePaymentService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orderService,
        private ShippingService $shipping,
        private PaymentGatewayService $paymentGateways,
        private StripePaymentService $stripePayments,
        private JazzCashPaymentService $jazzCashPayments,
        private PayPalPaymentService $paypalPayments,
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

        $this->paymentGateways->ensureSeeded();
        $this->paymentGateways->applyRuntimeConfig();

        $enabledMethods = $this->paymentGateways->enabledKeys();
        if ($enabledMethods === []) {
            return redirect()->route('cart.index')
                ->with('error', 'No payment methods are available. Please contact support.');
        }

        return view('shop.checkout', [
            'items' => $items,
            'subtotal' => $quote['subtotal'],
            'shipping' => $quote['shipping'],
            'total' => $quote['total'],
            'shippingCities' => $shippingCities,
            'selectedCityId' => $selectedCityId,
            'paymentMethods' => $this->paymentGateways->checkoutMethodLabels(),
            'easypaisa' => config('payments.easypaisa'),
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $this->paymentGateways->ensureSeeded();
        $this->paymentGateways->applyRuntimeConfig();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city_id' => 'required|exists:shipping_cities,id',
            'payment_method' => ['required', Rule::in($this->paymentGateways->enabledKeys())],
            'notes' => 'nullable|string|max:500',
        ]);

        $order = $this->orderService->createFromCart($validated);

        return $this->completeOrder($order, $validated['payment_method']);
    }

    private function completeOrder($order, string $method)
    {
        $this->paymentGateways->applyRuntimeConfig($method);

        if ($method === 'stripe') {
            $session = $this->stripePayments->createCheckoutSession($order);

            return redirect()->away($session->url);
        }

        if ($method === 'jazzcash') {
            $form = $this->jazzCashPayments->buildPaymentForm($order);

            return response()->view('shop.payments.jazzcash-redirect', [
                'endpoint' => $form['endpoint'],
                'fields' => $form['fields'],
            ]);
        }

        if ($method === 'paypal') {
            $approvalUrl = $this->paypalPayments->createCheckoutApprovalUrl($order);

            return redirect()->away($approvalUrl);
        }

        $this->cart->clear();
        session(['last_order_id' => $order->id]);

        if ($method === 'cod') {
            $order->update(['status' => 'confirmed']);
        }

        $message = match ($method) {
            'cod' => 'Order placed successfully. Pay on delivery.',
            'easypaisa' => $this->easypaisaConfirmationMessage($order),
            default => 'Order placed successfully.',
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
