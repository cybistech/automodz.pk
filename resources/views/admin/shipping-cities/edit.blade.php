@extends('layouts.admin')

@section('title', 'Edit Shipping City')

@section('content')
<form action="{{ route('admin.shipping-cities.update', $shippingCity) }}" method="POST" class="card max-w-2xl p-6">
    @csrf @method('PUT')
    @include('admin.shipping-cities._form', ['shippingCity' => $shippingCity])
    <div class="mt-6 flex gap-3">
        <button type="submit" class="btn-primary">Update City</button>
        <a href="{{ route('admin.shipping-cities.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
