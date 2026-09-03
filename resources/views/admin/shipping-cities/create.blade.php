@extends('layouts.admin')

@section('title', 'Add Shipping City')

@section('content')
<form action="{{ route('admin.shipping-cities.store') }}" method="POST" class="card max-w-2xl p-6">
    @csrf
    @include('admin.shipping-cities._form')
    <div class="mt-6 flex gap-3">
        <button type="submit" class="btn-primary">Save City</button>
        <a href="{{ route('admin.shipping-cities.index') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
