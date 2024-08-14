@extends('layouts.admin')

@section('title', 'Review Message')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Review Your Message</h1>

    <!-- Display the filters and message for review -->
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Recipients</h2>
        <p><strong>Broadcast To:</strong> {{ ucfirst($data['broadcast_type']) }}</p>
        <p><strong>Campus:</strong> {{ $campus }}</p>

        @if ($data['broadcast_type'] === 'students' || $data['broadcast_type'] === 'all')
            <p><strong>College:</strong> {{ $filterNames['college'] }}</p>
            <p><strong>Program:</strong> {{ $filterNames['program'] }}</p>
            <p><strong>Year:</strong> {{ $filterNames['year'] }}</p>
        @endif

        @if ($data['broadcast_type'] === 'employees' || $data['broadcast_type'] === 'all')
            <p><strong>Office:</strong> {{ $filterNames['office'] }}</p>
            <p><strong>Status:</strong> {{ $filterNames['status'] }}</p>
            <p><strong>Type:</strong> {{ $filterNames['type'] }}</p>
        @endif
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Message</h2>
        <p>{{ $data['message'] }}</p>
    </div>

    <!-- Schedule Review Section -->
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Schedule</h2>
        <p><strong>Schedule Type:</strong> {{ $data['schedule_type'] === 'immediate' ? 'Send Now' : 'Schedule for Later' }}
        </p>
        @if ($data['schedule_type'] === 'scheduled')
            <p><strong>Scheduled Time:</strong> {{ \Carbon\Carbon::parse($data['scheduled_at'])->format('F j, Y g:i A') }}
            </p>
        @endif
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
        @endif

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Confirm and Send</button>
    </form>

    <!-- Edit Message Button -->
    <a href="{{ route('admin.messages', $data) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg ml-2">Edit
        Message</a>
@endsection
