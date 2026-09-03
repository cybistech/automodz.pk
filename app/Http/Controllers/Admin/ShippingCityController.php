<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCity;
use Illuminate\Http\Request;

class ShippingCityController extends Controller
{
    public function index()
    {
        $cities = ShippingCity::orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.shipping-cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.shipping-cities.create');
    }

    public function store(Request $request)
    {
        ShippingCity::create($this->validated($request));

        return redirect()->route('admin.shipping-cities.index')
            ->with('success', 'Shipping city added.');
    }

    public function edit(ShippingCity $shippingCity)
    {
        return view('admin.shipping-cities.edit', compact('shippingCity'));
    }

    public function update(Request $request, ShippingCity $shippingCity)
    {
        $shippingCity->update($this->validated($request, $shippingCity->id));

        return redirect()->route('admin.shipping-cities.index')
            ->with('success', 'Shipping city updated.');
    }

    public function destroy(ShippingCity $shippingCity)
    {
        $shippingCity->delete();

        return redirect()->route('admin.shipping-cities.index')
            ->with('success', 'Shipping city deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $nameRule = 'required|string|max:100|unique:shipping_cities,name';
        if ($ignoreId) {
            $nameRule .= ','.$ignoreId;
        }

        $data = $request->validate([
            'name' => $nameRule,
            'distance_km' => 'required|numeric|min:0',
            'base_fee' => 'required|numeric|min:0',
            'rate_per_km' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['rate_per_km'] = $data['rate_per_km'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
