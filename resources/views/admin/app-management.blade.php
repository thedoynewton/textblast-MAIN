@extends('layouts.admin')

@section('title', 'App Management')

@section('content')
    <h1 class="text-3xl font-bold mb-6">App Management</h1>

    <!-- Update Button for Students -->
    <div class="mb-4">
        <button type="button" class="mt-4 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
            Import Students Database
        </button>
    </div>

    <!-- Students Table -->
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">First Name</th>
                    <th class="py-2 px-4 border-b">Last Name</th>
                    <th class="py-2 px-4 border-b">Middle Name</th>
                    <th class="py-2 px-4 border-b">Contact</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Campus</th>
                    <th class="py-2 px-4 border-b">College</th>
                    <th class="py-2 px-4 border-b">Program</th>
                    <th class="py-2 px-4 border-b">Major</th>
                    <th class="py-2 px-4 border-b">Enrollment Status</th>
                    <th class="py-2 px-4 border-b">Year</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4 border-b">{{ $student->stud_fname }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->stud_lname }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->stud_mname }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->stud_contact }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->stud_email }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->campus->campus_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->college->college_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->program->program_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->major->major_name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->enrollment_stat }}</td>
                        <td class="py-2 px-4 border-b">{{ $student->year->year_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Update Button for Employees -->
    <div class="mb-4">
        <button type="button" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
            Import Employees Database
        </button>
    </div>

    <!-- Employees Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">First Name</th>
                    <th class="py-2 px-4 border-b">Last Name</th>
                    <th class="py-2 px-4 border-b">Middle Name</th>
                    <th class="py-2 px-4 border-b">Contact</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Campus</th>
                    <th class="py-2 px-4 border-b">Office</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4 border-b">{{ $employee->emp_fname }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->emp_lname }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->emp_mname }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->emp_contact }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->emp_email }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->campus->campus_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->office->office_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->status->status_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $employee->type->type_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
