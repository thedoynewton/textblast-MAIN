@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Messages</h1>
    <!-- Display Success or Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Broadcasting Form -->
    <form action="{{ route('admin.broadcastToRecipients') }}" method="POST">
        @csrf

        <!-- Broadcast Type Selection -->
        <div class="mb-4">
            <label for="broadcast_type" class="block text-sm font-medium text-gray-700">Broadcast To</label>
            <select name="broadcast_type" id="broadcast_type" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" onchange="toggleFilters()">
                <option value="students">Students</option>
                <option value="employees">Employees</option>
                <option value="both">Both</option>
            </select>
        </div>

        <!-- Student-specific Filters -->
<div id="student_filters" style="display:none;">
    <div class="mb-4">
        <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
        <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
            <option value="">Select Campus</option>
            @foreach($campuses as $campus)
                <option value="{{ $campus['campus_id'] }}">{{ $campus['campus_name'] }}</option>
            @endforeach
        </select>   
    </div>

    <div class="mb-4">
        <label for="college" class="block text-sm font-medium text-gray-700">College</label>
        <select name="college" id="college" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
            <option value="">Select College</option>
            @foreach($colleges as $college)
                <option value="{{ $college['college_id'] }}">{{ $college['college_name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
        <select name="program" id="program" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
            <option value="">Select Program</option>
            @foreach($programs as $program)
                <option value="{{ $program['program_id'] }}">{{ $program['program_name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
        <select name="year" id="year" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
            <option value="">Select Year</option>
            @foreach($years as $year)
            <option value="{{ $year->year_id }}">{{ $year->year_name }}</option>
        @endforeach
        </select>
    </div>
</div>


        <!-- Employee-specific Filters -->

        <div id="employee_filters" style="display:none;">

            <div class="mb-4">
                <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
                <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Campus</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                    @endforeach
                </select>   
            </div>

            <div class="mb-4">
                <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                <select name="office" id="office" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                    <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" id="type" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Type</option>
                    @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Message Input -->
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" id="message" rows="4" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm"></textarea>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Send</button>
    </form>

    <!-- Directly embedded JavaScript -->
    <script>
        function toggleFilters() {
            var broadcastType = document.getElementById('broadcast_type').value;
            var studentFilters = document.getElementById('student_filters');
            var employeeFilters = document.getElementById('employee_filters');

            if (broadcastType === 'students') {
                studentFilters.style.display = 'block';
                employeeFilters.style.display = 'none';
            } else if (broadcastType === 'employees') {
                studentFilters.style.display = 'none';
                employeeFilters.style.display = 'block';
            } else {
                studentFilters.style.display = 'block';
                employeeFilters.style.display = 'block';
            }
        }

        // Initialize filters on page load
        window.onload = toggleFilters;
    </script>
@endsection

