@extends('layouts.subadmin')

@section('content')
    <div class="container mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8 transition-transform duration-200 hover:scale-101">

            <!-- Number of Messages and Balance -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Messages Sent Card -->
                <div class="bg-red-50 p-4 border-l-4 border-[#d50600] rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-[#d50600]">Total Messages Sent to Recipients</h2>
                    <p class="text-2xl font-semibold text-[#d50600]">{{ $totalRecipients }}</p>
                </div>

                <!-- Scheduled Messages Sent Card -->
                <div id="scheduledMessagesSentCard" class="bg-red-100 p-4 border-l-4 border-[#b10000] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer">
                    <h2 class="text-xl font-bold text-[#b10000]">Scheduled Messages Sent</h2>
                    <p class="text-2xl font-semibold text-[#b10000]">{{ $scheduledSentRecipients }}</p>
                </div>

                <!-- Immediate Messages Sent Card -->
                <div id="immediateMessagesSentCard" class="bg-yellow-100 p-4 border-l-4 border-[#d1a700] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer">
                    <h2 class="text-xl font-bold text-[#d1a700]">Immediate Messages Sent</h2>
                    <p class="text-2xl font-semibold text-[#d1a700]">{{ $immediateSentRecipients }}</p>
                </div>

                <!-- Failed Messages Card -->
                <div id="failedMessagesCard" class="bg-red-200 p-4 border-l-4 border-[#990000] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer">
                    <h2 class="text-xl font-bold text-[#990000]">Failed Messages</h2>
                    <p class="text-2xl font-semibold text-[#990000]">{{ $totalFailedRecipients }}</p>
                </div>

                <!-- Cancelled Messages Card -->
                <div class="bg-gray-200 p-4 border-l-4 border-[#6b7280] rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-[#6b7280]">Cancelled Messages</h2>
                    <p class="text-2xl font-semibold text-[#6b7280]">{{ $totalCancelled }}</p>
                </div>

                <!-- Pending Messages Card -->
                <div class="bg-orange-100 p-4 border-l-4 border-[#e07b00] rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-[#e07b00]">Pending Messages</h2>
                    <p class="text-2xl font-semibold text-[#e07b00]">{{ $totalPending }}</p>
                </div>

                  <!-- Remaining Account Balance Card -->
                <div class="bg-purple-100 p-4 border-l-4 border-[#7e57c2] rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-[#7e57c2]">Remaining Account Balance</h2>
                    <p class="text-2xl font-semibold text-[#7e57c2]">{{ $balance }}</p>
                </div>
            </div>

        <!-- Message Logs Section -->
        <h2 class="text-2xl font-bold mb-4 border-b-2 border-[#9d1e18] pb-2">Message Logs</h2>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
            <!-- Search Bar -->
            <div class="flex flex-col">
                <label for="search" class="block text-sm font-medium text-gray-700">Search Logs</label>
                <input type="text" id="search" placeholder="Search for logs..."
                    class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2 focus:border-[#d50600] focus:ring-[#d50600] transition duration-150">
            </div>
            <!-- Recipient Type Filter -->
            <div class="flex flex-col">
                <label for="recipientType" class="block text-sm font-medium text-gray-700">Filter Recipient</label>
                <select id="recipientType" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2 focus:border-[#d50600] focus:ring-[#d50600] transition duration-150">
                    <option value="all" selected>All Recipients</option>
                    <option value="students">Students</option>
                    <option value="employees">Employees</option>
                </select>
            </div>
            <!-- Message Status Filter -->
            <div class="flex flex-col">
                <label for="messageType" class="block text-sm font-medium text-gray-700">Filter Message Status</label>
                <select id="messageType" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm p-2 focus:border-[#d50600] focus:ring-[#d50600] transition duration-150">
                    <option value="all" selected>All Message Status</option>
                    <option value="sent">Sent</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <!-- Generate Logs Button -->
            <div class="flex flex-col items-start">
                <label for="generateLogs" class="block text-sm font-medium text-gray-700 mb-1">Generate Message Logs Report</label>
                <x-button class="w-full transform hover:scale-105 transition duration-200" color="red" id="generateLogs">Generate Report</x-button>
            </div>
        </div>

        <!-- Message Logs Table -->
        <div class="overflow-x-auto max-h-[450px] rounded-lg shadow-md border border-gray-300">
            <table id="messageLogsTable" class="min-w-full bg-white divide-y divide-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach([
                        'User', 'Recipient', 'Message', 'Sent Type', 'Campus', 'Created',
                        'Scheduled Date', 'Date Sent', 'Date Cancelled', 'Status',
                        'Total Recipients', 'Successful Deliveries', 'Failed Messages', 'Actions'
                        ] as $header)
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse ($messageLogs as $log)
                    <tr class="hover:bg-red-100 transition duration-150 ease-in-out"> <!-- Retaining red hover effect -->
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->user->name }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->recipient_type }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700">
                            {{ \Illuminate\Support\Str::limit($log->content, 20, '...') }}
                            @if (strlen($log->content) > 70)
                            <a href="#" class="text-[#9d1e18] hover:underline" data-modal-target="#messageLogsModal" data-template-name="Details" data-content="{{ $log->content }}">
                                <br>Read More
                            </a>
                            @endif
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->schedule }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->campus ? $log->campus->campus_name : 'N/A' }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->created_at->format('F j, Y g:i A') }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->scheduled_at ? $log->scheduled_at->format('F j, Y g:i A') : 'N/A' }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->sent_at ? $log->sent_at->format('F j, Y g:i A') : 'N/A' }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->cancelled_at ? $log->cancelled_at->format('F j, Y g:i A') : 'N/A' }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->status }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 text-center">{{ $log->total_recipients }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 text-center">{{ $log->sent_count }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 text-center">{{ $log->failed_count }}</td>
                        <td class="py-3 px-4 border-b text-gray-600">
                            @if ($log->status === 'Pending')
                            <form action="{{ route('subadmin.cancelScheduledMessage', $log->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-red-500 hover:underline">Cancel</button>
                            </form>
                            @else
                            <span class="text-gray-400">Cannot Cancel</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-4 text-gray-500">No message logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Include the Modal Component for Logs -->
        <x-modal modal-id="messageLogsModal" title="Message Log" content="Exciting News!"></x-modal>
    </div>
</div>

    <!-- Modal HTML Structure -->
    <div id="recipientModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-3/4 md:w-1/2 lg:w-1/3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b px-4 py-2">
                <h3 class="text-lg font-semibold">Recipients Details</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-800">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div id="recipientContent" class="p-4 max-h-80 overflow-y-auto">
                <!-- Recipient details will be dynamically populated here -->
            </div>

            <!-- Modal Footer -->
            <div class="border-t px-4 py-2 flex justify-end">
                <button id="closeModalFooter" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Close
                </button>
            </div>
        </div>
        <!-- Inject user role data into JavaScript -->
        <script>
            const baseUrl = @json(Auth::user()->role === 'subadmin' ? '/subadmin/recipients' : '/subadmin/recipients');
        </script>
    </div>

@vite([
'resources/js/app.css',
'resources/js/searchMessageLogs.js',
'resources/js/modal.js'])
@endsection
