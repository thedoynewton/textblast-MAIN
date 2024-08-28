@extends('layouts.admin')

@section('title', 'App Management')

@section('content')

{{-- Import Button --}}
<button type="button"
    class="btn btn-primary absolute right-8 top-7 bg-green-500 py-2 px-4 text-white font-bold rounded-lg shadow-md hover:bg-green-600 hover:shadow-lg hover:text-gray-100">
    Import
</button>

<div class="container mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-lg">

        <!-- Tabs -->
        <div class="mb-4">
            <div class="flex border-b border-gray-300">
                <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 focus:outline-none" data-value="contacts">
                    CONTACTS
                </button>
                <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 focus:outline-none" data-value="messageTemplates">
                    MESSAGE TEMPLATES
                </button>
                <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 focus:outline-none" data-value="messageLogs">
                    MESSAGE LOGS
                </button>
            </div>
        </div>

        <!-- Hidden Input to Store Selected Tab -->
        <input type="hidden" name="selected_tab" id="selected_tab" value="contacts">

        <!-- Contacts Tab -->
        <div id="contacts" class="tab-content">
            <!-- Filters Selection (Updated to be inline) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label for="contactsSearch" class="block text-sm font-medium text-gray-700">Search Contacts</label>
                    <input type="text" id="contactsSearch" placeholder="Search for contacts..."
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                </div>

                <div>
                    <label for="campus" class="block text-sm font-medium text-gray-700">Select Campus</label>
                    <select name="campus" id="campus"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="all">All Campuses</option>
                        @foreach ($campuses as $campus)
                        <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700">Filter By</label>
                    <select name="filter" id="filter"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="all">All Contacts</option>
                        <option value="students">Students</option>
                        <option value="employees">Employees</option>
                    </select>
                </div>
            </div>

            <!-- Contacts Table -->
            <div class="overflow-x-auto overflow-y-auto max-h-96 mb-8">
                <table id="contactsTable" class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b font-semibold text-gray-500">First Name</th>
                            <th class="py-3 px-4 border-b font-semibold text-gray-500">Last Name</th>
                            <th class="py-3 px-4 border-b font-semibold text-gray-500">Middle Name</th>
                            <th class="py-3 px-4 border-b font-semibold text-gray-500">Contact</th>
                            <th class="py-3 px-4 border-b font-semibold text-gray-500">Email</th>
                        </tr>
                    </thead>
                    <tbody id="contactsTableBody">
                        <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Message Templates Tab -->
        <div id="messageTemplates" class="tab-content hidden">
            <!-- Add Message Template Button -->
            <div class="mb-4">
                <a href="{{ route('message_templates.create') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out">Add New Template</a>
            </div>

            <!-- Message Templates Table -->
            <div class="overflow-x-auto overflow-y-auto max-h-96 mb-8">
                <table id="messageTemplatesTable" class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Template Name</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Message Content</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messageTemplates as $template)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="py-3 px-4 border-b text-gray-600">{{ $template->name }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $template->content }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">
                                <a href="{{ route('message_templates.edit', $template->id) }}"
                                    class="text-blue-500 hover:underline">Edit</a>
                                <form action="{{ route('message_templates.destroy', $template->id) }}"
                                    method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                        @if ($messageTemplates->isEmpty())
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No message templates found.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Message Logs Tab -->
        <div id="messageLogs" class="tab-content hidden">
            <!-- Message Logs Table -->
            <div class="overflow-x-auto overflow-y-auto max-h-96 mb-8">
                <table id="messageLogsTable" class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">User</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Recipient Type</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Message</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Message Type</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Created At</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Scheduled At</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Sent At</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Cancelled At</th> <!-- Added Cancelled At column -->
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Status</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Total Recipients</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Successful Deliveries</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Failed Messages</th>
                            <th class="py-3 px-4 border-b font-medium text-gray-700">Actions</th> <!-- Added Actions column -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messageLogs as $log)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->user->name }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->recipient_type }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->content }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->schedule }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->created_at->format('F j, Y g:i A') }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->scheduled_at ? $log->scheduled_at->format('F j, Y g:i A') : 'N/A' }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->sent_at ? $log->sent_at->format('F j, Y g:i A') : 'N/A' }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->cancelled_at ? $log->cancelled_at->format('F j, Y g:i A') : 'N/A' }}</td> <!-- Display Cancelled At -->
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->status }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->total_recipients }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->sent_count }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">{{ $log->failed_count }}</td>
                            <td class="py-3 px-4 border-b text-gray-600">
                                @if ($log->status === 'Pending')
                                    <form action="{{ route('admin.cancelScheduledMessage', $log->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:underline">Cancel</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">Cannot Cancel</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if ($messageLogs->isEmpty())
                        <tr>
                            <td colspan="13" class="text-center py-4 text-gray-500">No message logs found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/app-management.js'])
@endsection
