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

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Total Messages Sent</h2>
                <p class="text-2xl font-semibold" id="total-sent">{{ $totalMessagesSent }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Failed Messages</h2>
                <p class="text-2xl font-semibold" id="total-failed">{{ $failedMessagesSent }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Scheduled Messages</h2>
                <p class="text-2xl font-semibold" id="total-scheduled">{{ $scheduledMessagesSent }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Immediate Messages</h2>
                <p class="text-2xl font-semibold" id="total-immediate">{{ $immediateMessagesSent }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Remaining Balance</h2>
                <p class="text-2xl font-semibold" id="remaining-balance">{{ $balance }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
