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

        <!-- Default Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <!-- Date Range Filter -->
            <div class="col-span-1">
                <label for="date-range" class="block text-sm font-medium text-gray-700">Date Range:</label>
                <select id="date-range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="last_7_days">Last 7 Days</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="last_3_months">Last 3 Months</option>
                </select>
            </div>

            <!-- Campus Filter -->
            <div class="col-span-1">
                <label for="campus" class="block text-sm font-medium text-gray-700">Campus:</label>
                <select id="campus" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" disabled selected>Select Campus</option>
                    <option value="all">All Campuses</option>
                    @foreach ($campuses as $campus)
                    <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Recipient Type Filter -->
            <div class="col-span-1">
                <label for="recipient-type" class="block text-sm font-medium text-gray-700">Recipient Type:</label>
                <select id="recipient-type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" disabled selected>Select Recipient Type</option>
                    <option value="both">Both</option>
                    <option value="student">Student</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
        </div>

        <!-- Conditional Filters Based on Recipient Type -->
        <div id="student-filters" class="mb-4 hidden">
            <h1 class="text-lg text-black font-semibold">Student Filters</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- College Selection -->
                <div class="col-span-1">
                    <label for="college" class="block text-sm font-medium text-gray-700">Academic Unit</label>
                    <select name="college" id="college" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" disabled>
                        <option value="" disabled selected>Select Academic Unit</option>
                    </select>
                </div>

                <!-- Program Selection -->
                <div class="col-span-1">
                    <label for="program" class="block text-sm font-medium text-gray-700">Academic Program</label>
                    <select name="program" id="program" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" disabled>
                        <option value="" disabled selected>Select Program</option>
                    </select>
                </div>

                <!-- Year Level Selection -->
                <div class="col-span-1">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year Level</label>
                    <select name="year" id="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled selected>Select Year</option>
                        @foreach ($years as $year)
                        <option value="{{ $year->year_id }}">{{ $year->year_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div id="employee-filters" class="mb-4 hidden">
            <h1 class="text-lg text-black font-semibold">Employee Filters</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Office Selection -->
                <div class="col-span-1">
                    <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                    <select name="office" id="office" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled selected>Select Office</option>
                        <option value="all">All Offices</option>
                        @foreach ($offices as $office)
                        <option value="{{ $office->office_id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Selection -->
                <div class="col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled selected>Select Status</option>
                        <option value="all">All Statuses</option>
                        @foreach ($statuses as $status)
                        <option value="{{ $status->status_id }}">{{ $status->status_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Selection -->
                <div class="col-span-1">
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled selected>Select Type</option>
                        <option value="all">All Types</option>
                        @foreach ($types as $type)
                        <option value="{{ $type->type_id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Chart View -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Messages Sent Over Time</h2>
                <div class="relative inline-block text-left">
                    <button id="exportChartButton" class="inline-flex justify-center w-full rounded-md border-gray-300 shadow-sm px-4 py-2 bg-blue-500 text-white text-sm font-medium hover:bg-blue-700">
                        Generate Report
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/docx/7.0.0/docx.min.js"></script>

@vite(['resources/js/analytics.js'])
@endsection