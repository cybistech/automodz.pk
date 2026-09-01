<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GuestOrderService;
use Illuminate\Http\Request;

class GuestOrderController extends Controller
{
    public function __construct(private GuestOrderService $guestOrders) {}

    public function trackForm()
    {
        return view('shop.orders.track');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'contact' => 'required|string',
        ]);

        $contact = trim($request->contact);

        $order = Order::where('order_number', strtoupper($request->order_number))
            ->where(function ($q) use ($contact) {
                $q->where('customer_email', $contact)
                    ->orWhere('customer_phone', $contact);
            })
            ->first();

        if (! $order) {
            return back()->withInput()->with('error', 'Order not found. Please check your order number and email/phone.');
        }

        session(['last_order_id' => $order->id]);

        return redirect()->route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ]);
    }
}
