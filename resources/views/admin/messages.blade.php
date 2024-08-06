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
            <option value="all">All Recipients</option>
        </select>
    </div>

    <!-- Campus Selection (Always Visible) -->
    <div class="mb-4" id="campus_filter">
        <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
        <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" onchange="updateDependentFilters()">
            <option value="">Select Campus</option>
            @foreach($campuses as $campus)
            <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Additional Filters for "All Recipients" -->
    <div id="all_recipients_filters" style="display:none;">
        <div class="mb-4">
            <label for="recipient_type" class="block text-sm font-medium text-gray-700">Recipient Type</label>
            <select name="recipient_type" id="recipient_type" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="students">All Students</option>
                <option value="employees">All Employees</option>
                <option value="both">Both Students and Employees</option>
            </select>
        </div>
    </div>

    <!-- Student-specific Filters -->
    <div id="student_filters" style="display:none;">
        <div class="mb-4">
            <label for="college" class="block text-sm font-medium text-gray-700">College</label>
            <select name="college" id="college" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select College</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
            <select name="program" id="program" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select Program</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
            <select name="year" id="year" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select Year</option>
            </select>
        </div>
    </div>

    <!-- Employee-specific Filters -->
    <div id="employee_filters" style="display:none;">
        <div class="mb-4">
            <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
            <select name="office" id="office" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select Office</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select Status</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
            <select name="type" id="type" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select Type</option>
            </select>
        </div>
    </div>

    <!-- Message Timing Option -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Message Timing</label>
        <div class="flex items-center">
            <input type="radio" name="timing" value="realtime" id="realtime" class="mr-2" checked>
            <label for="realtime" class="mr-4">Send Now</label>

            <input type="radio" name="timing" value="scheduled" id="schedule" class="mr-2">
            <label for="schedule">Schedule</label>
        </div>
    </div>

    <!-- Date-Time Picker for Scheduling -->
    <div id="schedule_time_container" class="mb-4" style="display:none;">
        <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Schedule Date & Time</label>
        <input type="datetime-local" name="scheduled_time" id="scheduled_time" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
    </div>


    <!-- Message Input -->
    <div class="mb-4">
        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
        <textarea name="message" id="message" rows="4" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm"></textarea>
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Send Message</button>
</form>

<!-- Directly embedded JavaScript -->
<script>
    function toggleFilters() {
        var broadcastType = document.getElementById('broadcast_type').value;
        var studentFilters = document.getElementById('student_filters');
        var employeeFilters = document.getElementById('employee_filters');
        var allRecipientsFilters = document.getElementById('all_recipients_filters');
        var campusSelect = document.getElementById('campus');
        var scheduleTimeContainer = document.getElementById('schedule_time_container');

        // Preserve the current selections
        var selectedCampus = campusSelect.value;
        var selectedCollege = document.getElementById('college').value;
        var selectedProgram = document.getElementById('program').value;
        var selectedYear = document.getElementById('year').value;
        var selectedOffice = document.getElementById('office').value;
        var selectedStatus = document.getElementById('status').value;
        var selectedType = document.getElementById('type').value;
        var selectedRecipientType = document.getElementById('recipient_type') ? document.getElementById('recipient_type').value : null;

        // Show or hide filters based on the broadcast type
        if (broadcastType === 'students') {
            studentFilters.style.display = 'block';
            employeeFilters.style.display = 'none';
            allRecipientsFilters.style.display = 'none';
        } else if (broadcastType === 'employees') {
            studentFilters.style.display = 'none';
            employeeFilters.style.display = 'block';
            allRecipientsFilters.style.display = 'none';
        } else {
            studentFilters.style.display = 'none';
            employeeFilters.style.display = 'none';
            allRecipientsFilters.style.display = 'block';
        }

        // Show or hide the schedule time container based on the selected timing
        var timing = document.querySelector('input[name="timing"]:checked').value;
        if (timing === 'scheduled') {
            scheduleTimeContainer.style.display = 'block';
        } else {
            scheduleTimeContainer.style.display = 'none';
        }

        // Reapply the previous selections to preserve state
        campusSelect.value = selectedCampus;
        document.getElementById('college').value = selectedCollege;
        document.getElementById('program').value = selectedProgram;
        document.getElementById('year').value = selectedYear;
        document.getElementById('office').value = selectedOffice;
        document.getElementById('status').value = selectedStatus;
        document.getElementById('type').value = selectedType;
        if (selectedRecipientType) {
            document.getElementById('recipient_type').value = selectedRecipientType;
        }

        // Trigger update for dependent filters
        if (selectedCampus) {
            updateDependentFilters();
        }
    }

    function updateDependentFilters() {
        var campusId = document.getElementById('campus').value;
        var broadcastType = document.getElementById('broadcast_type').value;
        var recipientType = document.getElementById('recipient_type') ? document.getElementById('recipient_type').value : null;

        // Preserve selections before updating options
        var selectedCollege = document.getElementById('college').value;
        var selectedProgram = document.getElementById('program').value;
        var selectedYear = document.getElementById('year').value;
        var selectedOffice = document.getElementById('office').value;
        var selectedStatus = document.getElementById('status').value;
        var selectedType = document.getElementById('type').value;

        // Reset dependent filters
        document.getElementById('college').innerHTML = '<option value="">Select College</option>';
        document.getElementById('program').innerHTML = '<option value="">Select Program</option>';
        document.getElementById('year').innerHTML = '<option value="">Select Year</option>';
        document.getElementById('office').innerHTML = '<option value="">Select Office</option>';
        document.getElementById('status').innerHTML = '<option value="">Select Status</option>';
        document.getElementById('type').innerHTML = '<option value="">Select Type</option>';

        if (campusId) {
            fetch(`/api/filters/${broadcastType}/${campusId}?recipient_type=${recipientType}`)
                .then(response => response.json())
                .then(data => {
                    if (broadcastType === 'students' || (broadcastType === 'all' && recipientType === 'students') || recipientType === 'both') {
                        updateSelectOptions('college', data.colleges, selectedCollege);
                        updateSelectOptions('program', data.programs, selectedProgram);
                        updateSelectOptions('year', data.years, selectedYear);
                    }
                    if (broadcastType === 'employees' || (broadcastType === 'all' && recipientType === 'employees') || recipientType === 'both') {
                        updateSelectOptions('office', data.offices, selectedOffice);
                        updateSelectOptions('status', data.statuses, selectedStatus);
                        updateSelectOptions('type', data.types, selectedType);
                    }
                });
        }
    }

    function updateSelectOptions(selectId, options, selectedValue) {
        var select = document.getElementById(selectId);
        options.forEach(option => {
            var opt = document.createElement('option');
            opt.value = option.id;
            opt.textContent = option.name;
            if (option.id == selectedValue) {
                opt.selected = true; // Preserve the selected value
            }
            select.appendChild(opt);
        });
    }

    // Initialize filters and timing options on page load
    window.onload = function() {
        toggleFilters();

        // Add event listener for timing radio buttons
        var timingRadios = document.querySelectorAll('input[name="timing"]');
        timingRadios.forEach(function(radio) {
            radio.addEventListener('change', toggleFilters);
        });
    };
</script>

@endsection