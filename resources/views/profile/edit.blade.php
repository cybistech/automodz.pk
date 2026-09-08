@extends('layouts.account')

@section('title', 'Profile')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="card p-6 sm:p-8">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="card p-6 sm:p-8">
        @include('profile.partials.update-password-form')
    </div>

    <div class="card border-red-500/20 p-6 sm:p-8">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
