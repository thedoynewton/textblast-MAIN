@extends('layouts.admin')

@section('title', 'Review Message')

@section('content')

<div class="flex items-start justify-between bg-white p-6 rounded-lg shadow-md">
    <div class="w-2/3">
        <!-- Display the filters and message for review -->
        <div class="mb-4">
            <div class="mb-2">
                <label class="block mb-2 text-md font-medium text-gray-700">Sending To</label>
                <p class="border rounded-md p-2">
                    {{ trim($campus) }},
                    @if ($data['broadcast_type'] === 'students' || $data['broadcast_type'] === 'all')
                    {{ trim($filterNames['college']) }},
                    {{ trim($filterNames['program']) }},
                    {{ trim($filterNames['major']) }},
                    {{ trim($filterNames['year']) }},
                    @endif
                    @if ($data['broadcast_type'] === 'employees' || $data['broadcast_type'] === 'all')
                    {{ trim($filterNames['office']) }},
                    {{ trim($filterNames['status']) }},
                    {{ trim($filterNames['type']) }},
                    @endif
                </p>
            </div>
            <div class="text-right mb-3">
                <small class="font-medium text-gray-700">Total Recipients: {{ $totalRecipients }}</small>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-md font-medium text-gray-700">Scheduled For</label>
                <p class="border rounded-md p-2">
                    {{ $data['schedule_type'] === 'immediate' ? 'Now' : \Carbon\Carbon::parse($data['scheduled_at'])->format('F j, Y g:i A') }}
                </p>
            </div>
        </div>

        <!-- Display the message -->
        <div class="mb-6">
            <h2 class="text-xl mb-2 font-semibold">Message</h2>
            <!-- Message container with fixed height and border -->
            <div class="border border-gray-300 p-4 rounded-md overflow-y-auto" style="height: 18rem;">
                <p class="text-sm leading-relaxed">
                    {!! nl2br(e($data['message'])) !!}
                </p>
            </div>
        </div>

        <!-- Form to confirm and send the message -->
        <form action="{{ route('admin.broadcastToRecipients') }}" method="POST" style="display: inline;">
            @csrf
            <!-- Hidden inputs to pass the original data -->
            <input type="hidden" name="broadcast_type" value="{{ $data['broadcast_type'] }}">
            <input type="hidden" name="campus" value="{{ $data['campus'] }}">
            <input type="hidden" name="message" value="{{ $data['message'] }}">
            <input type="hidden" name="schedule" value="{{ $data['schedule_type'] }}">

            @if ($data['schedule_type'] === 'scheduled')
            <input type="hidden" name="scheduled_date" value="{{ $data['scheduled_at'] }}">
            @endif

            <!-- Include other hidden fields as necessary -->
            @if (isset($data['college']))
            <input type="hidden" name="college" value="{{ $data['college'] }}">
            @endif

            @if (isset($data['program']))
            <input type="hidden" name="program" value="{{ $data['program'] }}">
            @endif

            @if (isset($data['major'])) <!-- Added Major field -->
            <input type="hidden" name="major" value="{{ $data['major'] }}">
            @endif

            @if (isset($data['year']))
            <input type="hidden" name="year" value="{{ $data['year'] }}">
            @endif

            @if (isset($data['office']))
            <input type="hidden" name="office" value="{{ $data['office'] }}">
            @endif

            @if (isset($data['status']))
            <input type="hidden" name="status" value="{{ $data['status'] }}">
            @endif

            @if (isset($data['type']))
            <input type="hidden" name="type" value="{{ $data['type'] }}">

            <input type="hidden" name="total_recipients" value="{{ $totalRecipients }}">
            @endif

            <!-- Edit Message Button -->
            <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg mr-2">
                <a href="{{ route('admin.messages', $data) }}">Edit Message</a>
            </button>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Confirm and Send</button>
        </form>
    </div>

    <div class="w-1/3 flex justify-end">
        <div class="relative" style="width: 18rem;">
            <!-- iPhone Mockup Image -->
            <img src="{{ asset('images/iPhone15Mockup.png') }}" alt="iPhone Mockup" class="w-full h-auto">

            <!-- User Icon and Name -->
            <div class="absolute top-[10%] left-[10%] w-[80%] h-[10%] flex flex-col items-center justify-center space-y-1">
                <!-- User Icon -->
                <img src="{{ asset('images/profile-user.png') }}" alt="User Icon" class="w-6 h-6">
                <!-- User Name -->
                <span class="font-regular text-gray-900 mt-1" style="font-size: 9px;">USeP</span>
            </div>

            <!-- Message Content -->
            <div class="absolute top-[20%] left-[14%] w-[80%] h-[65%] p-2 text-left bg-transparent overflow-y-auto space-y-1">
                <div class="bg-gray-200 p-2 text-gray-900" style="font-size: 9px; line-height: 1.3; max-width: 80%; display: inline-block; border-radius: 0.5rem;">
                    {!! nl2br(e($data['message'])) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection