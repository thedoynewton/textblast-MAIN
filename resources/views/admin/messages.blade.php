@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
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

    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Broadcasting Form -->
        <form action="{{ route('admin.reviewMessage') }}" method="POST">
            @csrf

            <!-- Broadcast Type Selection as Tabs -->
            <div class="mb-4">
                <div class="flex border-b border-gray-300">
                    <button type="button" class="tab-button px-4 py-2 text-sm font-medium focus:outline-none" data-value="all">ALL</button>
                    <button type="button" class="tab-button px-4 py-2 text-sm font-medium focus:outline-none" data-value="students">STUDENTS</button>
                    <button type="button" class="tab-button px-4 py-2 text-sm font-medium focus:outline-none" data-value="employees">EMPLOYEES</button>
                </div>
                <input type="hidden" name="broadcast_type" id="broadcast_type" value="{{ request('broadcast_type', 'all') }}">
            </div>

            <!-- Campus Selection (Always Visible) -->
            <div class="mb-4" id="campus_filter">
                <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
                <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="" disabled selected>Campus</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Student-specific Filters -->
            <div id="student_filters" style="display: none;">
                <div class="mb-4">
                    <label for="college" class="block text-sm font-medium text-gray-700">College</label>
                    <select name="college" id="college" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" onchange="updateProgramDropdown()">
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
                        @foreach($years as $year)
                            <option value="{{ $year->year_id }}">{{ $year->year_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Employee-specific Filters -->
            <div id="employee_filters" style="display: none;">
                <div class="mb-4">
                    <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                    <select name="office" id="office" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm" onchange="updateStatusDropdown()">
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

            <!-- Message Input -->
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="message" placeholder="Enter your message here ..." rows="4" class="block w-full mt-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 focus:ring-indigo-300 p-2 text-sm overflow-y-auto resize-none" style="height: 14rem">{{ request('message') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg">Review</button>
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
                        document.getElementById('broadcast_type').value = this.getAttribute('data-value');

                        // Highlight the active tab and remove highlight from others
                        document.querySelectorAll('.tab-button').forEach(function(btn) {
                            btn.classList.remove('border-b-2', 'border-indigo-500', 'text-indigo-500');
                        });
                        this.classList.add('border-b-2', 'border-indigo-500', 'text-indigo-500');

                        // Toggle the filters based on the selected tab
                        toggleFilters();
                    });
                });

                // Add event listener for campus dropdown change
                document.getElementById('campus').addEventListener('change', updateDependentFilters);
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

            function updateDependentFilters() {
                var campusId = document.getElementById('campus').value;
                var broadcastType = document.getElementById('broadcast_type').value;

                if (!campusId) return;

                // Make an AJAX request to get the dependent filters based on the selected campus
                fetch(`/api/filters/${broadcastType}/${campusId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (broadcastType === 'students') {
                            updateSelectOptions('college', data.colleges);
                            updateSelectOptions('year', data.years);  // Ensure years are always populated
                        } else if (broadcastType === 'employees') {
                            updateSelectOptions('office', data.offices);
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
                select.innerHTML = '<option value="">Select ' + selectId.charAt(0).toUpperCase() + selectId.slice(1) + '</option>';
            }

            function updateProgramDropdown() {
                var collegeId = document.getElementById('college').value;

                // Reset the program dropdown
                clearDropdownOptions('program');

                if (collegeId) {
                    // Make an AJAX request to get the dependent programs based on the selected college
                    fetch(`/api/filters/college/${collegeId}/programs`)
                        .then(response => response.json())
                        .then(data => {
                            updateSelectOptions('program', data.programs);
                        });
                }
            }

            function updateStatusDropdown() {
                var officeId = document.getElementById('office').value;

                // Reset the type dropdown
                clearDropdownOptions('type');

                if (officeId) {
                    // Make an AJAX request to get the dependent types based on the selected office
                    fetch(`/api/filters/office/${officeId}/types`)
                        .then(response => response.json())
                        .then(data => {
                            updateSelectOptions('type', data.types);
                        });
                }
            }
        </script>
    </div>
@endsection
