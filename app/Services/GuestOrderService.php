<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class GuestOrderService
{
    public function linkOrdersToUser(User $user): int
    {
        $query = Order::whereNull('user_id');

        $query->where(function ($q) use ($user) {
            if ($user->email) {
                $q->orWhere('customer_email', $user->email);
            }
            if ($user->phone) {
                $q->orWhere('customer_phone', $user->phone);
            }
        });

        return $query->update(['user_id' => $user->id]);
    }

    public function canViewOrder(Order $order, ?User $user = null, ?string $guestToken = null, ?int $sessionOrderId = null): bool
    {
        if ($user?->isAdmin()) {
            return true;
        }

        if ($user && $order->user_id === $user->id) {
            return true;
        }

        if ($guestToken && hash_equals($order->guest_token ?? '', $guestToken)) {
            return true;
        }

        if ($sessionOrderId && $sessionOrderId === $order->id) {
            return true;
        }

        return false;
    }
}
