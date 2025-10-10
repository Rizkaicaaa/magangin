@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-6 bg-white shadow sm:rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Ubah Password</h2>
            @include('profile.partials.update-password-form')
        </div>
    </div>
</div>
@endsection