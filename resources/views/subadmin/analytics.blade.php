@extends('layouts.subadmin')

@section('title', 'Analytics')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6"></h1>

        <!-- Date Range Filter -->
        <div class="mb-4">
            <label for="date-range" class="block text-sm font-medium text-gray-700">Select Date Range:</label>
            <select id="date-range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="last_3_months">Last 3 Months</option>
            </select>
        </div>

    </div>
</div>
@endsection