@extends('layouts.subadmin')

@section('title', 'Messages')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Messages</h1>
    <p>Here you can manage your messages.</p>

    <!-- Broadcasting Form -->
    <form action="{{ route('admin.broadcastToEmployees') }}" method="POST">
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
                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="college" class="block text-sm font-medium text-gray-700">College</label>
                <select name="college" id="college" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select College</option>
                    @foreach($colleges as $college)
                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
                <select name="program" id="program" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Program</option>
                    @foreach($programs as $program)
                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                <select name="year" id="year" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Year</option>
                    @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Employee-specific Filters -->
        <div id="employee_filters" style="display:none;">
            <div class="mb-4">
                <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                <select name="office" id="office" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" id="type" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Type</option>
                    @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
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
@endsection

@section('scripts')
    @vite(['resources/js/message-filters.js'])
@endsection
