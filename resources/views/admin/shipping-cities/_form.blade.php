@php $shippingCity = $shippingCity ?? null; @endphp
<div class="space-y-4">
    <div>
        <label class="text-sm text-slate-400">City Name</label>
        <input type="text" name="name" value="{{ old('name', $shippingCity?->name) }}" required class="input-field mt-1">
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="text-sm text-slate-400">Distance from {{ config('shipping.origin_city') }} (km)</label>
            <input type="number" step="0.01" min="0" name="distance_km" value="{{ old('distance_km', $shippingCity?->distance_km ?? 0) }}" required class="input-field mt-1">
        </div>
        <div>
            <label class="text-sm text-slate-400">Base Fee (Rs.)</label>
            <input type="number" step="0.01" min="0" name="base_fee" value="{{ old('base_fee', $shippingCity?->base_fee ?? 0) }}" required class="input-field mt-1">
        </div>
        <div>
            <label class="text-sm text-slate-400">Rate per km (Rs.)</label>
            <input type="number" step="0.01" min="0" name="rate_per_km" value="{{ old('rate_per_km', $shippingCity?->rate_per_km ?? 0) }}" class="input-field mt-1">
            <p class="mt-1 text-xs text-slate-500">Shipping = base fee + (distance × rate per km)</p>
        </div>
    </div>
    <div>
        <label class="text-sm text-slate-400">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $shippingCity?->sort_order ?? 0) }}" class="input-field mt-1 w-32">
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $shippingCity?->is_active ?? true)) class="rounded text-orange-500">
        <span class="text-sm">Active</span>
    </label>
</div>
