@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="card max-w-2xl p-6">
    @csrf
    @include('admin.categories._form')
    <button type="submit" class="btn-primary mt-6">Create Category</button>
</form>
@endsection
