@extends('layouts.admin')

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

        <!-- Filters for Start Date, End Date, and Campus -->
        <div class="flex space-x-4 mb-4">
            <div>
                <label for="start-date" class="block text-sm font-medium">Start Date</label>
                <input type="date" id="start-date" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
            </div>
            <div>
                <label for="end-date" class="block text-sm font-medium">End Date</label>
                <input type="date" id="end-date" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
            </div>
            <div>
                <label for="campus-filter" class="block text-sm font-medium">Campus</label>
                <select id="campus-filter" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                    <option value="" selected>All Campuses</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button id="apply-filters" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md">Apply Filters</button>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="mb-8">
            <canvas id="messagesChart" width="400" height="200"></canvas>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/analytics.js'])
@endsection
