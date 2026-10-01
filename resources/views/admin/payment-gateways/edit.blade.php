@extends('layouts.admin')

@section('title', 'Payment Gateways')

@section('content')
<div class="max-w-4xl">
    <p class="text-sm text-slate-400">
        Enable checkout payment options and store gateway credentials. Only enabled gateways with required fields filled in appear on checkout.
    </p>

    <form action="{{ route('admin.payment-gateways.update') }}" method="POST" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        @foreach($gateways as $gateway)
            @php($definition = $definitions[$gateway->key] ?? [])
            <div class="card p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ $gateway->label }}</h2>
                        @if(! empty($definition['description']))
                            <p class="mt-1 text-xs text-slate-500">{{ $definition['description'] }}</p>
                        @endif
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input
                            type="checkbox"
                            name="gateways[{{ $gateway->key }}][is_enabled]"
                            value="1"
                            class="rounded text-orange-500"
                            @checked(old("gateways.{$gateway->key}.is_enabled", $gateway->is_enabled))
                        >
                        Active
                    </label>
                </div>

                @if(! empty($definition['fields']))
                    <div class="mt-4 grid gap-4">
                        @foreach($definition['fields'] as $fieldKey => $field)
                            <div>
                                <label class="text-sm text-slate-400">{{ $field['label'] }}</label>
                                <input
                                    type="{{ $field['type'] ?? 'text' }}"
                                    name="gateways[{{ $gateway->key }}][settings][{{ $fieldKey }}]"
                                    value="{{ old("gateways.{$gateway->key}.settings.{$fieldKey}", ($field['secret'] ?? false) ? '' : $gateway->setting($fieldKey)) }}"
                                    placeholder="{{ ($field['secret'] ?? false) && $gateway->setting($fieldKey) ? '•••••••• (leave blank to keep current)' : '' }}"
                                    class="input-field mt-1 font-mono text-sm"
                                    autocomplete="off"
                                >
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-xs text-slate-500">No credentials required for this method.</p>
                @endif

                @if($gateway->key === 'jazzcash')
                    <p class="mt-3 text-xs text-slate-500">Return URL: <span class="font-mono text-slate-400">{{ route('payment.jazzcash.return') }}</span></p>
                @elseif($gateway->key === 'stripe')
                    <p class="mt-3 text-xs text-slate-500">Success URL uses route <span class="font-mono text-slate-400">payment.stripe.success</span></p>
                @elseif($gateway->key === 'paypal')
                    <p class="mt-3 text-xs text-slate-500">Return URL: <span class="font-mono text-slate-400">{{ url('/payment/paypal/success/{order}') }}</span></p>
                @endif
            </div>
        @endforeach

        <button type="submit" class="btn-primary">Save payment settings</button>
    </form>
</div>
@endsection
