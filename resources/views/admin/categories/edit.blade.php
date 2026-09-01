@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="card max-w-2xl p-6">
    @csrf @method('PUT')
    @include('admin.categories._form')
    <button type="submit" class="btn-primary mt-6">Update Category</button>
</form>
@endsection
