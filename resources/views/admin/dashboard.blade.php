@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <p class="text-gray-700">Welcome to the Admin Dashboard. Here you can manage your application.</p>
        
        <!-- Number of Messages and Balance Cards -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Total Messages Sent to Recipients</h2>
                <p class="text-2xl font-semibold" id="total-sent">0</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Failed Messages</h2>
                <p class="text-2xl font-semibold" id="total-failed">0</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Scheduled Messages</h2>
                <p class="text-2xl font-semibold" id="total-scheduled">0</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Immediate Messages</h2>
                <p class="text-2xl font-semibold" id="total-immediate">0</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Cancelled Messages</h2>
                <p class="text-2xl font-semibold" id="total-cancelled">0</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Remaining Balance</h2>
                <p class="text-2xl font-semibold" id="remaining-balance">{{ $balance }}</p>
            </div>
        </div>
    </div>

</div>
@vite(['resources/js/analytics.js'])
@endsection
