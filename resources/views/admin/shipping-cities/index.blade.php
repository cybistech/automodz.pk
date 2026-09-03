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
                        <a href="{{ route('admin.shipping-cities.edit', $city) }}" class="text-orange-400 hover:text-orange-300">Edit</a>
                        <form action="{{ route('admin.shipping-cities.destroy', $city) }}" method="POST" class="inline" onsubmit="return confirm('Delete this shipping city?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="ml-3 text-red-400 hover:text-red-300">Delete</button>
                        </form>
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
