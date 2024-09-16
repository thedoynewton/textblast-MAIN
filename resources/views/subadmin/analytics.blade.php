@extends('layouts.subadmin')

@section('title', 'Analytics')

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

        <!-- Date Range Filter -->
        <div class="mb-4">
            <label for="date-range" class="block text-sm font-medium text-gray-700">Select Date Range:</label>
            <select id="date-range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="last_3_months">Last 3 Months</option>
            </select>
        </div>

        <!-- Number of Messages and Balance -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Total Messages Sent</h2>
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

        <!-- Chart View -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Messages Sent Over Time</h2>
                <div class="relative inline-block text-left">
                    <button id="exportChartButton" class="inline-flex justify-center w-full rounded-md border-gray-300 shadow-sm px-4 py-2 bg-blue-500 text-white text-sm font-medium hover:bg-blue-700">
                        Export Chart
                    </button>
                    <div id="exportDropdown" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                        <div class="py-1">
                            <a href="#" id="exportPNG" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as PNG</a>
                            <a href="#" id="exportExcel" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as Excel</a>
                        </div>
                    </div>
                </div>
            </div>
            <canvas id="messagesChart" height="100"></canvas>
        </div>
        <!-- End of Chart View -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/docx/7.0.0/docx.min.js"></script>
@vite(['resources/js/analytics.js'])
@endsection