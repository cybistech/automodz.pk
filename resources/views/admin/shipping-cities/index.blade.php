@extends('layouts.admin')

@section('title', 'Shipping Cities')

@section('content')
<div class="flex items-center justify-between">
    <div>
        <p class="text-slate-400">{{ $cities->total() }} cities configured</p>
        <p class="mt-1 text-xs text-slate-500">Origin warehouse: {{ config('shipping.origin_city') }}</p>
    </div>
    <a href="{{ route('admin.shipping-cities.create') }}" class="btn-primary">Add City</a>
</div>

<div class="card mt-6 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="border-b border-slate-700 bg-slate-800/50">
            <tr>
                <th class="px-4 py-3 text-left">City</th>
                <th class="px-4 py-3 text-left">Distance</th>
                <th class="px-4 py-3 text-left">Base Fee</th>
                <th class="px-4 py-3 text-left">Rate/km</th>
                <th class="px-4 py-3 text-left">Shipping</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cities as $city)
                <tr class="border-b border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $city->name }}</td>
                    <td class="px-4 py-3">{{ number_format($city->distance_km) }} km</td>
                    <td class="px-4 py-3">Rs. {{ number_format($city->base_fee) }}</td>
                    <td class="px-4 py-3">Rs. {{ number_format($city->rate_per_km) }}</td>
                    <td class="px-4 py-3 font-medium text-orange-400">Rs. {{ number_format($city->shippingFee()) }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $city->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $city->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.shipping-cities.edit', $city) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-700 text-orange-400 transition hover:border-orange-500/50 hover:bg-orange-500/10" title="Edit" aria-label="Edit shipping city">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.shipping-cities.destroy', $city) }}" method="POST" class="inline" onsubmit="return confirm('Delete this shipping city?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-700 text-red-400 transition hover:border-red-500/50 hover:bg-red-500/10" title="Delete" aria-label="Delete shipping city">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">No shipping cities yet. Add cities to enable checkout shipping.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $cities->links() }}</div>
@endsection
