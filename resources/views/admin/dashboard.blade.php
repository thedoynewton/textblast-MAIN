@extends('layouts.admin')

@section('title', 'Admin Dashboard')

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

            <!-- Number of Messages and Balance -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <h2 class="text-xl font-bold">Total Messages Sent to Recipients</h2>
                    <p class="text-2xl font-semibold">{{ $totalSent }}</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <h2 class="text-xl font-bold">Failed Messages</h2>
                    <p class="text-2xl font-semibold">{{ $totalFailed }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <h2 class="text-xl font-bold">Scheduled Messages</h2>
                    <p class="text-2xl font-semibold">{{ $totalScheduled }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <h2 class="text-xl font-bold">Immediate Messages</h2>
                    <p class="text-2xl font-semibold">{{ $totalImmediate }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <h2 class="text-xl font-bold">Cancelled Messages</h2>
                    <p class="text-2xl font-semibold">{{ $totalCancelled }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <h2 class="text-xl font-bold">Remaining Balance</h2>
                    <p class="text-2xl font-semibold">{{ $balance }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
