@props(['order'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold '.$order->statusBadgeClass()]) }}>
    {{ $order->statusLabel() }}
</span>
