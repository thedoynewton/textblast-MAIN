@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
    <!-- Display Success or Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Broadcasting Form -->
        <form action="{{ route('admin.reviewMessage') }}" method="POST">
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
                <input type="hidden" name="broadcast_type" id="broadcast_type"
                    value="{{ request('broadcast_type', 'all') }}">
            </div>

            <!-- Campus Selection (Always Visible) -->
            <div class="mb-4" id="campus_filter">
                <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
                <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="" disabled selected>Select Campus</option>
                    <option value="all">All Campuses</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Student-specific Filters -->
            <div id="student_filters" style="display: none;">
                <div class="mb-4">
                    <label for="college" class="block text-sm font-medium text-gray-700">College</label>
                    <select name="college" id="college"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm"
                        onchange="updateProgramDropdown()">
                        <option value="" disabled selected>Select College</option>
                        <option value="all">All Colleges</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="program" class="block text-sm font-medium text-gray-700">Academic Program</label>
                    <select name="program" id="program"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                        <option value="" disabled selected>Select Program</option>
                        <option value="all">All Programs</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" id="year"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                        <option value="" disabled selected>Select Year</option>
                        <option value="all">All Year Levels</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->year_id }}">{{ $year->year_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Employee-specific Filters -->
            <div id="employee_filters" style="display: none;">
                <div class="mb-4">
                    <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                    <select name="office" id="office"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm"
                        onchange="updateTypeDropdown()">
                        <option value="" disabled selected>Select Office</option>
                        <option value="all">All Offices</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm"
                        onchange="updateTypeDropdown()">
                        <option value="" disabled selected>Select Status</option>
                        <option value="all">All Statuses</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="type"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                        <option value="" disabled selected>Select Type</option>
                        <option value="all">All Types</option>
                    </select>
                </div>
            </div>

            <!-- Message Template Selection -->
            <div class="mb-4">
                <label for="template" class="block text-sm font-medium text-gray-700">Select Template</label>
                <select id="template" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="" disabled selected>Select a Template</option>
                    @foreach ($messageTemplates as $template)
                        <option value="{{ $template->content }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Message Input -->
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="message" placeholder="Enter your message here ..." rows="4"
                    class="block w-full mt-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-300 p-2 text-sm overflow-y-auto resize-none"
                    style="height: 14rem">{{ request('message') }}</textarea>
            </div>

            <!-- Schedule Options -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Schedule</label>
                <div class="flex items-center mt-2">
                    <input type="radio" id="immediate" name="schedule" value="immediate" checked>
                    <label for="immediate" class="ml-2">Send Immediately</label>
                </div>
                <div class="flex items-center mt-2">
                    <input type="radio" id="scheduled" name="schedule" value="scheduled">
                    <label for="scheduled" class="ml-2">Schedule for Later</label>
                </div>
            </div>

            <!-- Date and Time Picker for Scheduling -->
            <div id="schedule-options" style="display: none;" class="mb-4">
                <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Select Date and Time</label>
                <input type="datetime-local" id="scheduled_date" name="scheduled_date"
                    class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
            </div>


            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg">Review Message</button>
            </div>
        </form>

        <!-- Directly embedded JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize filters on page load
                toggleFilters();

                // Add event listeners to the tab buttons
                document.querySelectorAll('.tab-button').forEach(function(button) {
                    button.addEventListener('click', function() {
                        // Update the hidden broadcast_type input based on the clicked tab
                        document.getElementById('broadcast_type').value = this.getAttribute(
                            'data-value');

                        // Highlight the active tab and remove highlight from others
                        document.querySelectorAll('.tab-button').forEach(function(btn) {
                            btn.classList.remove('border-b-2', 'border-indigo-500',
                                'text-indigo-500');
                        });
                        this.classList.add('border-b-2', 'border-indigo-500', 'text-indigo-500');

                        // Reset the Campus dropdown to its default placeholder
                        resetCampusDropdown();

                        // Toggle the filters based on the selected tab
                        toggleFilters();
                    });
                });

                // Add event listeners for dropdown changes
                document.getElementById('campus').addEventListener('change', updateDependentFilters);
                document.getElementById('office').addEventListener('change', updateTypeDropdown);
                document.getElementById('status').addEventListener('change', updateTypeDropdown);

                // Add event listener for template selection
                document.getElementById('template').addEventListener('change', function() {
                    const templateContent = this.value;
                    document.getElementById('message').value = templateContent;
                });

                // Add event listener for schedule options
                const scheduleRadios = document.querySelectorAll('input[name="schedule"]');
                const scheduleOptions = document.getElementById('schedule-options');

                scheduleRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'scheduled') {
                            scheduleOptions.style.display = 'block';
                        } else {
                            scheduleOptions.style.display = 'none';
                        }
                    });
                });
            });

            function toggleFilters() {
                var broadcastType = document.getElementById('broadcast_type').value;
                var studentFilters = document.getElementById('student_filters');
                var employeeFilters = document.getElementById('employee_filters');

                // Hide all filters initially
                studentFilters.style.display = 'none';
                employeeFilters.style.display = 'none';

                // Display the appropriate filters based on the broadcast type
                if (broadcastType === 'students') {
                    studentFilters.style.display = 'block';
                } else if (broadcastType === 'employees') {
                    employeeFilters.style.display = 'block';
                }

                // Clear dropdown values when switching tabs
                clearDropdownOptions('college');
                clearDropdownOptions('program');
                clearDropdownOptions('year');
                clearDropdownOptions('office');
                clearDropdownOptions('status');
                clearDropdownOptions('type');
            }

            function resetCampusDropdown() {
                var campusSelect = document.getElementById('campus');
                campusSelect.value = ''; // Reset to default "Select Campus"
            }

            function updateDependentFilters() {
                var campusId = document.getElementById('campus').value;
                var broadcastType = document.getElementById('broadcast_type').value;

                if (campusId === 'all') {
                    // If "All Campuses" is chosen, clear all other dropdowns
                    clearDropdownOptions('college');
                    clearDropdownOptions('program');
                    clearDropdownOptions('year');
                    clearDropdownOptions('office');
                    clearDropdownOptions('status');
                    clearDropdownOptions('type');
                    return;
                }

                if (!campusId) return;

                // Make an AJAX request to get the dependent filters based on the selected campus
                fetch(`/api/filters/${broadcastType}/${campusId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (broadcastType === 'students') {
                            updateSelectOptions('college', data.colleges);
                            updateSelectOptions('year', data.years); // Ensure years are always populated
                        } else if (broadcastType === 'employees') {
                            updateSelectOptions('office', data.offices);
                            updateSelectOptions('status', data.statuses); // Populate statuses for employees
                            updateSelectOptions('type', data.types); // Populate types for employees
                        }
                    });
            }

            function updateSelectOptions(selectId, options) {
                var select = document.getElementById(selectId);
                clearDropdownOptions(selectId);
                options.forEach(option => {
                    var opt = document.createElement('option');
                    opt.value = option.id;
                    opt.textContent = option.name;
                    select.appendChild(opt);
                });
            }

            function clearDropdownOptions(selectId) {
                var select = document.getElementById(selectId);
                select.innerHTML = '<option value="" disabled selected>Select ' + selectId.charAt(0).toUpperCase() + selectId
                    .slice(1) + '</option>';
                select.innerHTML += '<option value="all">All ' + selectId.charAt(0).toUpperCase() + selectId.slice(1) +
                    '</option>';
            }

            function updateProgramDropdown() {
                var collegeId = document.getElementById('college').value;

                // Reset the program dropdown
                clearDropdownOptions('program');

                if (collegeId === 'all') {
                    return;
                }

                if (collegeId) {
                    // Make an AJAX request to get the dependent programs based on the selected college
                    fetch(`/api/filters/college/${collegeId}/programs`)
                        .then(response => response.json())
                        .then(data => {
                            updateSelectOptions('program', data.programs);
                        });
                }
            }

            function updateTypeDropdown() {
                var campusId = document.getElementById('campus').value;
                var officeId = document.getElementById('office').value;
                var statusId = document.getElementById('status').value;

                // Reset the type dropdown
                clearDropdownOptions('type');

                if (campusId && officeId) {
                    // Make an AJAX request to get the dependent types based on the selected campus, office, and status
                    fetch(`/api/filters/types/${campusId}/${officeId}/${statusId}`)
                        .then(response => response.json())
                        .then(data => {
                            updateSelectOptions('type', data.types);
                        });
                }
            }
        </script>
    </div>
@endsection
