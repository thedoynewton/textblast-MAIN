@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<!-- Display Success or Error Messages -->
@if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}

    <!-- Progress Bar for Sending Messages -->
    @if (session('logId'))
    <div id="progress-container" data-log-id="{{ session('logId') }}" class="mt-4">
        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span id="progress-label" class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                        0% Sent
                    </span>
                </div>
                <div class="text-right">
                    <span id="progress-percent" class="text-xs font-semibold inline-block text-blue-600">
                        0%
                    </span>
                </div>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                <div id="progress-bar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

@if (session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    {{ session('error') }}
</div>
@endif

<div class="bg-white p-6 rounded-lg shadow-md">
    <!-- Broadcasting Form -->
    <form action="{{ route('admin.reviewMessage') }}" method="POST" id="broadcast-form" novalidate>
        @csrf

        <!-- Broadcast Type Selection as Tabs -->
        <div class="mb-4">
            <div class="flex border-b border-gray-300">
                <button type="button" class="tab-button px-4 py-2 text-sm font-medium focus:outline-none"
                    data-value="all">ALL</button>
                <button type="button" class="tab-button px-4 py-2 text-sm font-medium focus:outline-none"
                    data-value="students">STUDENTS</button>
                <button type="button" class="tab-button px-4 py-2 text-sm font-medium focus:outline-none"
                    data-value="employees">EMPLOYEES</button>
            </div>
            <input type="hidden" name="broadcast_type" id="broadcast_type" value="{{ request('broadcast_type', 'all') }}">
        </div>

        <!-- Filters Container -->
        <div class="mb-4">
            <div class="flex space-x-4 mb-4">

                <!-- Campus Selection (Always Visible) -->
                <div class="flex-grow" id="campus_filter">
                    <label for="campus" class="block text-sm font-medium">Campus</label>
                    <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="" disabled selected>Select Campus</option>
                        <option value="all">All Campuses</option>
                        @foreach ($campuses as $campus)
                        <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Message Template Selection -->
                <div class="flex-grow">
                    <label for="template" class="block text-sm font-medium">Select Template</label>
                    <select id="template" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="" disabled selected>Select a Template</option>
                        @foreach ($messageTemplates as $template)
                        <option value="{{ $template->content }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- End: class flex space-x-4 mb-4 -->

            <!-- Student-specific Filters -->
            <div class="flex space-x-4 mb-4" id="student_filters" style="display: none;">
                <div class="w-1/3">
                    <label for="college" class="block text-sm font-medium">Academic Unit</label>
                    <select name="college" id="college" required
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2"
                        onchange="updateProgramDropdown()">
                        <option value="" disabled selected>Select Academic Unit</option>
                        <option value="">Select Academic Unit</option>
                        <option value="all">All Academic Unit</option>
                    </select>
                </div>

                <div class="w-1/3">
                    <label for="program" class="block text-sm font-medium">Academic Program</label>
                    <select name="program" id="program" required
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="" disabled selected>Select Program</option>
                        <option value="all">All Programs</option>
                    </select>
                </div>

                <div class="w-1/3">
                    <label for="year" class="block text-sm font-medium">Year</label>
                    <select name="year" id="year" required
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="" disabled selected>Select Year</option>
                        <option value="all">All Year Levels</option>
                        @foreach ($years as $year)
                        <option value="{{ $year->year_id }}">{{ $year->year_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Employee-specific Filters -->
            <div class="flex space-x-4 mb-4" id="employee_filters" style="display: none;">
                <div class="w-1/3">
                    <label for="office" class="block text-sm font-medium">Office</label>
                    <select name="office" id="office" required
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2"
                        onchange="updateTypeDropdown()">
                        <option value="" disabled selected>Select Office</option>
                        <option value="all">All Offices</option>
                    </select>
                </div>

                <div class="w-1/3">
                    <label for="status" class="block text-sm font-medium">Status</label>
                    <select name="status" id="status" required
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2"
                        onchange="updateTypeDropdown()">
                        <option value="" disabled selected>Select Status</option>
                        <option value="all">All Statuses</option>
                    </select>
                </div>

                <div class="w-1/3">
                    <label for="type" class="block text-sm font-medium">Type</label>
                    <select name="type" id="type" required
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="" disabled selected>Select Type</option>
                        <option value="all">All Types</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- End: Filters Container -->

        <!-- Message Input -->
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" id="message" placeholder="Enter your message here ..." rows="4"
                class="block w-full mt-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-300 p-2 text-sm overflow-y-auto resize-none"
                style="color: var(--primary-text); height: 14rem" required>{{ request('message') }}</textarea>
        </div>

        <div class="mb-6 flex items-center space-x-8">
            <!-- Batch Size Input -->
            <div>
                <label for="batch_size" class="block text-sm font-medium">Batch Size</label>
                <input type="number" name="batch_size" id="batch_size" class="w-full border rounded-md shadow-sm p-1" value="1" min="1">
            </div>

            <!-- Display Total Recipients -->
            <div>
                <label class="block text-sm font-medium">Total Recipients</label>
                <input type="text" id="total_recipients" class="w-full p-1.5 border rounded-md shadow-sm text-center text-sm font-medium" readonly>
            </div>

            <!-- Send Message Options -->
            <div>
                <label class="block text-sm font-medium">Send Message</label>
                <div class="p-1 flex items-center space-x-2">
                    <input type="radio" id="immediate" name="schedule" value="immediate" checked>
                    <label for="immediate">Now</label>
                    <input type="radio" id="scheduled" name="schedule" value="scheduled">
                    <label for="scheduled">Send Later</label>
                </div>
            </div>

            <!-- Date and Time Picker for Scheduling -->
            <div id="schedule-options" style="display: none;">
                <label for="scheduled_date" class="block text-sm font-medium">Select Date and Time:</label>
                <input type="datetime-local" id="scheduled_date" name="scheduled_date" class="w-full h-8 border border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <div class="flex justify-end">
            <x-button type="submit" color="yellow">Review Message</x-button>
        </div>
    </form>
</div>
<!-- This loads the script in resources/js -->
@vite(['resources/js/messages.js', 'resources/js/messagesWarning.js'])
@endsection