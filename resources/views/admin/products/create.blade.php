@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form')
    <button type="submit" class="btn-primary mt-6">Create Product</button>
</form>
@endsection
