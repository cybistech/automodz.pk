@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.products._form')
    <button type="submit" class="btn-primary mt-6">Update Product</button>
</form>
@endsection
