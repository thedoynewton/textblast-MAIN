<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Campus;
use App\Models\Office;
use App\Models\Status;
use App\Models\College;
use App\Models\Program;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Type;
use App\Models\MessageTemplate;
use App\Models\MessageLog; // Import the MessageLog model
use Illuminate\Http\Request;
use App\Services\MoviderService;
use Illuminate\Support\Facades\Auth; // Import Auth to get the user ID
use Carbon\Carbon;
use App\Jobs\SendScheduledMessage; // Import the job for sending scheduled messages

class MessageController extends Controller
{
    protected $moviderService;

    public function __construct(MoviderService $moviderService)
    {
        $this->moviderService = $moviderService;
    }

    /**
     * Show the messaging form.
     */
    public function showMessagesForm()
    {
        $campuses = Campus::all();
        $years = Year::all();
        $messageTemplates = MessageTemplate::all();

        return view('admin.messages', compact('campuses', 'years', 'messageTemplates'));
    }

    /**
     * Show the review page for the message before broadcasting.
     */
    public function reviewMessage(Request $request)
{
    // Retrieve the form data
    $data = $request->all();

    // Handle the 'all' case for campus
    if ($data['campus'] === 'all') {
        $campus = 'All Campuses';
    } else {
        $campus = Campus::find($data['campus'])->campus_name ?? 'All Campuses';
    }

    // Initialize the filter names array
    $filterNames = [
        'college' => 'All Colleges',
        'program' => 'All Programs',
        'year' => 'All Years',
        'office' => 'All Offices',
        'status' => 'All Statuses',
        'type' => 'All Types'
    ];

    // Get the other filter names depending on the broadcast type
    if ($data['broadcast_type'] === 'students' || $data['broadcast_type'] === 'all') {
        if (isset($data['college']) && $data['college'] !== 'all') {
            $filterNames['college'] = College::find($data['college'])->college_name ?? 'All Colleges';
        }
        if (isset($data['program']) && $data['program'] !== 'all') {
            $filterNames['program'] = Program::find($data['program'])->program_name ?? 'All Programs';
        }
        if (isset($data['year']) && $data['year'] !== 'all') {
            $filterNames['year'] = Year::find($data['year'])->year_name ?? 'All Years';
        }
    }

    if ($data['broadcast_type'] === 'employees' || $data['broadcast_type'] === 'all') {
        if (isset($data['office']) && $data['office'] !== 'all') {
            $filterNames['office'] = Office::find($data['office'])->office_name ?? 'All Offices';
        }
        if (isset($data['status']) && $data['status'] !== 'all') {
            $filterNames['status'] = Status::find($data['status'])->status_name ?? 'All Statuses';
        }
        if (isset($data['type']) && $data['type'] !== 'all') {
            $filterNames['type'] = Type::find($data['type'])->type_name ?? 'All Types';
        }
    }

    // Ensure schedule_type and scheduled_at are passed to the view
    $data['schedule_type'] = $request->input('schedule', 'immediate');
    $data['scheduled_at'] = $request->input('scheduled_date');

    // Pass the data to the review view
    return view('admin.review-message', compact('data', 'campus', 'filterNames'));
}



    /**
     * Broadcast messages to either students, employees, or both.
     */
    public function broadcastToRecipients(Request $request)
    {
        $broadcastType = $request->broadcast_type;
        $scheduleType = $request->schedule; // 'immediate' or 'scheduled'
        $scheduledDate = $request->scheduled_date; // Will be null if scheduleType is 'immediate'
        $userId = Auth::id(); // Get the ID of the logged-in user

        if ($scheduleType === 'scheduled' && $scheduledDate) {
            // Schedule the message for later
            $scheduledAt = Carbon::parse($scheduledDate);
            $this->scheduleMessage($request, $scheduledAt, $userId);

            // Store a log of the scheduled message
            $this->logMessage($request, $userId, 'scheduled', $scheduledAt);

            return redirect()->route('admin.messages')->with('success', 'Message scheduled successfully.');
        } else {
            // Send the message immediately
            $this->sendMessageImmediately($request, $userId);

            return redirect()->route('admin.messages')->with('success', 'Messages sent successfully.');
        }
    }

    protected function sendMessageImmediately(Request $request, $userId)
    {
        $broadcastType = $request->broadcast_type;
        $successCount = 0;
        $errorCount = 0;
        $errorDetails = '';

        if ($broadcastType === 'students' || $broadcastType === 'all') {
            $studentResult = $this->sendBulkMessages($request, 'students');
            $successCount += $studentResult['successCount'];
            $errorCount += $studentResult['errorCount'];
            $errorDetails .= (string) $studentResult['errorDetails'];
        }

        if ($broadcastType === 'employees' || $broadcastType === 'all') {
            $employeeResult = $this->sendBulkMessages($request, 'employees');
            $successCount += $employeeResult['successCount'];
            $errorCount += $employeeResult['errorCount'];
            $errorDetails .= (string) $employeeResult['errorDetails'];
        }

        $this->logMessage($request, $userId, 'immediate');

        // Handle success or error messaging
        if ($successCount > 0) {
            session()->flash('success', "Messages sent successfully to $successCount recipients." . $errorDetails);
        } else {
            session()->flash('error', "Failed to send messages to $errorCount recipients." . $errorDetails);
        }
    }


    /**
     * Sends bulk messages to the specified recipient type (students or employees).
     */
    protected function sendBulkMessages(Request $request, $recipientType)
    {
        $query = $recipientType === 'students' ? Student::query() : Employee::query();

        // Handle the case where 'All Campuses' is selected
        if ($request->filled('campus') && $request->input('campus') !== 'all') {
            $query->where('campus_id', $request->input('campus'));
        }

        if ($recipientType === 'students') {
            if ($request->filled('college') && $request->input('college') !== 'all') {
                $query->where('college_id', $request->input('college'));
            }

            if ($request->filled('program') && $request->input('program') !== 'all') {
                $query->where('program_id', $request->input('program'));
            }

            if ($request->filled('year') && $request->input('year') !== 'all') {
                $query->where('year_id', $request->input('year'));
            }
        } else { // employees
            if ($request->filled('office') && $request->input('office') !== 'all') {
                $query->where('office_id', $request->input('office'));
            }

            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('status_id', $request->input('status'));
            }

            if ($request->filled('type') && $request->input('type') !== 'all') {
                $query->where('type_id', $request->input('type'));
            }
        }

        // Fetch all recipients matching the criteria
        $recipients = $query->get();
        $formattedRecipients = [];
        $invalidRecipients = [];

        foreach ($recipients as $recipient) {
            // Use appropriate fields for students and employees
            $number = $recipientType === 'students' ? $recipient->stud_contact : $recipient->emp_contact;
            $email = $recipientType === 'students' ? $recipient->stud_email : $recipient->emp_email;

            // Ensure number and email are not NULL
            $number = $number ?: 'N/A';
            $email = $email ?: 'N/A';

            // Validate and format the phone number
            $number = preg_replace('/\D/', '', $number); // Remove all non-digit characters
            $number = substr($number, -10); // Get the last 10 digits (which should be the actual phone number)

            if (strlen($number) === 10) {
                $formattedRecipients[] = '+63' . $number;
            } else {
                $invalidRecipients[] = [
                    'name' => $recipientType === 'students' ? $recipient->stud_name : $recipient->emp_name,
                    'number' => $number,
                    'email' => $email
                ];
            }
        }

        // Handle invalid recipients
        if (empty($formattedRecipients) && !empty($invalidRecipients)) {
            $errorMessage = 'The following numbers are invalid: ';
            foreach ($invalidRecipients as $invalidRecipient) {
                $errorMessage .= $invalidRecipient['name'] . ' (Number: ' . $invalidRecipient['number'] . ', Email: ' . $invalidRecipient['email'] . '), ';
            }
            $errorMessage = rtrim($errorMessage, ', ');
            return [
                'successCount' => 0,
                'errorCount' => count($invalidRecipients),
                'errorDetails' => $errorMessage
            ];
        }

        // Proceed with sending messages
        $message = $request->input('message');
        $batchSize = 100;
        $recipientBatches = array_chunk($formattedRecipients, $batchSize);
        $successCount = 0;
        $errorCount = 0;
        $batchErrors = [];

        foreach ($recipientBatches as $batch) {
            $response = $this->moviderService->sendBulkSMS($batch, $message);

            if (isset($response->phone_number_list) && !empty($response->phone_number_list)) {
                $successCount += count($response->phone_number_list);
            } else {
                $errorCount += count($batch);
                $batchErrors[] = $response->error->description ?? 'Unknown error';
            }
        }

        $errorDetails = '';
        if (!empty($invalidRecipients)) {
            $errorDetails = ' The following numbers are invalid: ';
            foreach ($invalidRecipients as $invalidRecipient) {
                $errorDetails .= $invalidRecipient['name'] . ' (Number: ' . $invalidRecipient['number'] . ', Email: ' . $invalidRecipient['email'] . '), ';
            }
            $errorDetails = rtrim($errorDetails, ', ');
        }

        if (!empty($batchErrors)) {
            $errorDetails .= ' ' . implode(', ', $batchErrors);
        }

        return [
            'successCount' => $successCount,
            'errorCount' => $errorCount,
            'errorDetails' => $errorDetails
        ];
    }


    protected function logMessage(Request $request, $userId, $scheduleType, $scheduledAt = null)
    {
        MessageLog::create([
            'user_id' => $userId,
            'recipient_type' => $request->broadcast_type,
            'content' => $request->message,
            'schedule' => $scheduleType === 'scheduled' && $scheduledAt ? 'scheduled' : 'immediate',
            'scheduled_at' => $scheduledAt,
            'created_at' => now(),
        ]);
    }


    protected function scheduleMessage(Request $request, Carbon $scheduledAt, $userId)
    {
        // Dispatch the job with the necessary data and delay
        SendScheduledMessage::dispatch($request->all(), $userId)->delay($scheduledAt);

        // Optionally log or take additional action here
        $this->logMessage($request, $userId, 'scheduled', $scheduledAt);
    }


    public function getMessageLogs()
    {
        $messageLogs = MessageLog::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.app-management', compact('messageLogs'));
    }
}
