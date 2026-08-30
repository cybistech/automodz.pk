<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && ! auth()->user()?->isAdmin()) {
            abort(403);
        }

        $order->load('items', 'payment');

        return view('shop.orders.show', compact('order'));
    }

    public function confirmation(Order $order)
    {
        $canView = session('last_order_id') === $order->id
            || (auth()->check() && $order->user_id === auth()->id())
            || auth()->user()?->isAdmin();

        if (! $canView) {
            abort(403);
        }

        $order->load('items', 'payment');

        return view('shop.orders.show', compact('order'));
    }
}
