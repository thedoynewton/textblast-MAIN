@extends('layouts.subadmin')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">

        <!-- Warning Message -->
        @if($lowBalance)
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <h2 class="text-xl font-bold">Warning: Low SMS Balance</h2>
            <p>Your SMS balance is running low. Please recharge to avoid service interruption.</p>
        </div>
        @endif

@vite(['resources/js/analytics.js'])
@endsection