@props(['order'])

@php $steps = $order->statusTimeline(); @endphp

<div {{ $attributes->merge(['class' => 'card p-5 sm:p-6']) }}>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="font-display text-lg font-bold text-white">Order status</h2>
        <div class="flex flex-wrap gap-2">
            <x-order-status-badge :order="$order" />
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $order->paymentBadgeClass() }}">
                {{ $order->paymentStatusLabel() }}
            </span>
        </div>
    </div>

    <ol class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach($steps as $index => $step)
            <li class="relative flex items-start gap-3 sm:flex-col sm:items-center sm:text-center">
                <span @class([
                    'relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-xs font-bold',
                    'border-orange-400 bg-orange-500 text-white' => $step['state'] === 'complete',
                    'border-orange-400 bg-orange-500/20 text-orange-300 ring-4 ring-orange-500/10' => $step['state'] === 'current',
                    'border-red-400 bg-red-500/20 text-red-300' => $step['state'] === 'cancelled',
                    'border-slate-600 bg-slate-900 text-slate-500' => $step['state'] === 'upcoming',
                ])>
                    @if($step['state'] === 'complete')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @elseif($step['state'] === 'cancelled')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </span>
                <div class="min-w-0 pt-1 sm:pt-2">
                    <p @class([
                        'text-sm font-semibold',
                        'text-white' => in_array($step['state'], ['complete', 'current', 'cancelled'], true),
                        'text-slate-500' => $step['state'] === 'upcoming',
                    ])>{{ $step['label'] }}</p>
                    @if($step['state'] === 'current')
                        <p class="mt-0.5 text-[11px] font-medium text-orange-400">Current</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</div>
