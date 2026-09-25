<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'guest_token',
        'user_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping',
        'tax',
        'total',
        'currency',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'notes',
        'bank_reference',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /** @return list<string> */
    public static function fulfillmentSteps(): array
    {
        return ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Payment Pending',
            'paid' => 'Paid',
            'failed' => 'Payment Failed',
            'refunded' => 'Refunded',
            default => ucfirst((string) $this->payment_status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/30',
            'confirmed' => 'bg-sky-500/15 text-sky-300 border-sky-500/30',
            'processing' => 'bg-orange-500/15 text-orange-300 border-orange-500/30',
            'shipped' => 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30',
            'delivered' => 'bg-green-500/15 text-green-300 border-green-500/30',
            'cancelled' => 'bg-red-500/15 text-red-300 border-red-500/30',
            default => 'bg-slate-700/80 text-slate-300 border-slate-600/50',
        };
    }

    public function paymentBadgeClass(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-green-500/15 text-green-300 border-green-500/30',
            'pending' => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/30',
            'failed' => 'bg-red-500/15 text-red-300 border-red-500/30',
            'refunded' => 'bg-slate-500/15 text-slate-300 border-slate-500/30',
            default => 'bg-slate-700/80 text-slate-300 border-slate-600/50',
        };
    }

    public function trackingNumber(): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->order_number));
    }

    public function parcelWeightKg(): float
    {
        if (! $this->relationLoaded('items')) {
            $this->load('items.product');
        }

        $weight = $this->items->sum(function (OrderItem $item): float {
            $unitWeight = (float) ($item->product?->weight ?? 0);

            return $unitWeight * $item->quantity;
        });

        return round($weight, 2);
    }

    public function codCollectAmount(): float
    {
        if ($this->payment_method === 'cod' && $this->payment_status !== 'paid') {
            return (float) $this->total;
        }

        return 0.0;
    }

    public function paymentModeLabel(): string
    {
        return config('payments.methods.'.$this->payment_method)
            ?? ucfirst(str_replace('_', ' ', (string) $this->payment_method));
    }

    public function shippingLabelOrderDate(): string
    {
        return $this->created_at?->format('d-m-Y') ?? now()->format('d-m-Y');
    }

    public function shippingLabelQrUrl(): string
    {
        return route('orders.confirmation', [
            'order' => $this,
            'token' => $this->guest_token,
        ]);
    }

    public function formattedReceiverAddress(): string
    {
        $province = config('shipping.receiver_province', 'Punjab');
        $country = config('shipping.receiver_country', 'Pakistan');

        return trim(sprintf(
            '%s / %s, %s, %s',
            $this->shipping_address,
            $this->shipping_city,
            $province,
            $country,
        ));
    }

    /**
     * Timeline steps for customer-facing order tracking.
     *
     * @return list<array{key: string, label: string, state: 'complete'|'current'|'upcoming'|'cancelled'}>
     */
    public function statusTimeline(): array
    {
        if ($this->status === 'cancelled') {
            return [
                ['key' => 'pending', 'label' => 'Placed', 'state' => 'complete'],
                ['key' => 'cancelled', 'label' => 'Cancelled', 'state' => 'cancelled'],
            ];
        }

        $steps = self::fulfillmentSteps();
        $currentIndex = array_search($this->status, $steps, true);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        return collect($steps)->map(function (string $key, int $index) use ($currentIndex) {
            $state = 'upcoming';
            if ($index < $currentIndex) {
                $state = 'complete';
            } elseif ($index === $currentIndex) {
                $state = 'current';
            }

            return [
                'key' => $key,
                'label' => match ($key) {
                    'pending' => 'Placed',
                    'confirmed' => 'Confirmed',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    default => ucfirst($key),
                },
                'state' => $state,
            ];
        })->values()->all();
    }
}
