<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GuestOrderService;

class OrderController extends Controller
{
    public function __construct(private GuestOrderService $guestOrders) {}

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->withCount('items')
            ->when(
                request()->filled('status'),
                fn ($q) => $q->where('status', request('status'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusCounts = Order::where('user_id', auth()->id())
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('shop.orders.index', compact('orders', 'statusCounts'));
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
        $canView = $this->guestOrders->canViewOrder(
            $order,
            auth()->user(),
            request('token'),
            session('last_order_id'),
        );

        if (! $canView) {
            abort(403);
        }

        $order->load('items', 'payment');

        return view('shop.orders.show', [
            'order' => $order,
            'isGuestConfirmation' => ! auth()->check(),
        ]);
    }
}
