@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <p class="text-gray-700">Welcome to the Admin Dashboard. Here you can manage your application.</p>
        </div>

        <!-- Number of Messages and Balance -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Total Messages Sent to Recipients</h2>
                <p class="text-2xl font-semibold">{{ $totalSent }}</p>
            </div>
            <div class="bg-indigo-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Scheduled Messages Sent</h2>
                <p class="text-2xl font-semibold">{{ $scheduledSent }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Failed Messages</h2>
                <p class="text-2xl font-semibold">{{ $totalFailed }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Immediate Messages Sent</h2>
                <p class="text-2xl font-semibold">{{ $totalImmediate }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Cancelled Messages</h2>
                <p class="text-2xl font-semibold">{{ $totalCancelled }}</p>
            </div>
            <div class="bg-orange-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Pending Messages</h2>
                <p class="text-2xl font-semibold">{{ $totalPending }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Remaining Account Balance</h2>
                <p class="text-2xl font-semibold">{{ $balance }}</p>
            </div>
        </div>

        <!-- Message Logs Section -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Message Logs</h2>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Search Bar -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Logs</label>
                    <input type="text" id="search" placeholder="Search for logs..."
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                </div>
                <!-- Recipient Type Filter -->
                <div>
                    <label for="recipientType" class="block text-sm font-medium text-gray-700">Filter Recipient</label>
                    <select id="recipientType" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="all" selected>All Recipients</option>
                        <option value="students">Students</option>
                        <option value="employees">Employees</option>
                    </select>
                </div>
                <!-- Message Type Filter -->
                <div>
                    <label for="messageType" class="block text-sm font-medium text-gray-700">Filter Message Type</label>
                    <select id="messageType" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2">
                        <option value="all" selected>All Message Types</option>
                        <option value="immediate">Immediate</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Message Logs Table -->
            <div class="overflow-x-auto overflow-y-auto max-h-96 mb-8">
                <table id="messageLogsTable"
                    class="min-w-full bg-white border border-gray-300 rounded-lg divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">User</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Recipient</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Message</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Message Type</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Campus</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Created</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Scheduled Date</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Date Sent</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Date Cancelled</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Status</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Total Recipients</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Successful Deliveries</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Failed Messages</th>
                            <th class="py-3 px-4 border-b font-medium text-left text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messageLogs as $log)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">{{ $log->user->name }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">{{ $log->recipient_type }}
                                </td>
                                <td class="py-3 px-4 border-b text-gray-600">{{ $log->content }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">{{ $log->schedule }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">
                                    {{ $log->campus ? $log->campus->campus_name : 'N/A' }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">
                                    {{ $log->created_at->format('F j, Y g:i A') }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">
                                    {{ $log->scheduled_at ? $log->scheduled_at->format('F j, Y g:i A') : 'N/A' }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">
                                    {{ $log->sent_at ? $log->sent_at->format('F j, Y g:i A') : 'N/A' }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">
                                    {{ $log->cancelled_at ? $log->cancelled_at->format('F j, Y g:i A') : 'N/A' }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">{{ $log->status }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 text-center">{{ $log->total_recipients }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 text-center">{{ $log->sent_count }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 text-center">{{ $log->failed_count }}</td>
                                <td class="py-3 px-4 border-b text-gray-600 whitespace-nowrap">
                                    @if ($log->status === 'Pending')
                                        <form action="{{ route('admin.cancelScheduledMessage', $log->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:underline">
                                                <div class="rounded-full bg-red-500 p-2 hover:bg-red-600"
                                                    title="Cancel Send">
                                                    <img src="/images/cancel.png" alt="Remove Access" class="h-5 w-5"
                                                        style="filter: brightness(0) invert(1);">
                                                </div>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">N/A</span>
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

    @vite(['resources/js/app.css', 'resources/js/app-management.js', 'resources/js/searchMessageLogs.js', 'resources/js/modal.js'])
@endsection
