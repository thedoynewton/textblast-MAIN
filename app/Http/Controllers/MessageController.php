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
use App\Models\MessageLog;
use Illuminate\Http\Request;
use App\Services\MoviderService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Jobs\SendScheduledMessage;

class MessageController extends Controller
{
    protected $moviderService;

    public function __construct(MoviderService $moviderService)
    {
        $this->moviderService = $moviderService;
    }

    public function showMessagesForm()
    {
        $campuses = Campus::all();
        $years = Year::all();
        $messageTemplates = MessageTemplate::all();

        return view('admin.messages', compact('campuses', 'years', 'messageTemplates'));
    }

    public function reviewMessage(Request $request)
    {
        $data = $request->all();

        $campus = $data['campus'] === 'all' ? 'All Campuses' : Campus::find($data['campus'])->campus_name ?? 'All Campuses';

        $filterNames = [
            'college' => 'All Colleges',
            'program' => 'All Programs',
            'year' => 'All Years',
            'office' => 'All Offices',
            'status' => 'All Statuses',
            'type' => 'All Types'
        ];

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

        $data['schedule_type'] = $request->input('schedule', 'immediate');
        $data['scheduled_at'] = $request->input('scheduled_date');

        return view('admin.review-message', compact('data', 'campus', 'filterNames'));
    }

    public function broadcastToRecipients(Request $request)
    {
        $broadcastType = $request->broadcast_type;
        $scheduleType = $request->schedule;
        $scheduledDate = $request->scheduled_date;
        $userId = Auth::id();

        if ($scheduleType === 'scheduled' && $scheduledDate) {
            $scheduledAt = Carbon::parse($scheduledDate);
            $this->scheduleMessage($request, $scheduledAt, $userId);

            return redirect()->route('admin.messages')->with('success', 'Message scheduled successfully.');
        } else {
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

        if ($successCount > 0) {
            session()->flash('success', "Messages sent successfully to $successCount recipients." . $errorDetails);
        } else {
            session()->flash('error', "Failed to send messages to $errorCount recipients." . $errorDetails);
        }
    }

    protected function sendBulkMessages(Request $request, $recipientType)
    {
        $query = $recipientType === 'students' ? Student::query() : Employee::query();

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
        } else {
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

        $recipients = $query->get();
        $formattedRecipients = [];
        $invalidRecipients = [];

        foreach ($recipients as $recipient) {
            $number = $recipientType === 'students' ? $recipient->stud_contact : $recipient->emp_contact;

            $number = preg_replace('/\D/', '', $number);
            $number = substr($number, -10);

            if (strlen($number) === 10) {
                $formattedRecipients[] = '+63' . $number;
            } else {
                $invalidRecipients[] = [
                    'name' => $recipientType === 'students' ? $recipient->stud_name : $recipient->emp_name,
                    'number' => $number,
                ];
            }
        }

        if (empty($formattedRecipients) && !empty($invalidRecipients)) {
            $errorMessage = 'The following numbers are invalid: ';
            foreach ($invalidRecipients as $invalidRecipient) {
                $errorMessage .= $invalidRecipient['name'] . ' (Number: ' . $invalidRecipient['number'] . '), ';
            }
            $errorMessage = rtrim($errorMessage, ', ');
            return [
                'successCount' => 0,
                'errorCount' => count($invalidRecipients),
                'errorDetails' => $errorMessage
            ];
        }

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
                $errorDetails .= $invalidRecipient['name'] . ' (Number: ' . $invalidRecipient['number'] . '), ';
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
        // Ensure the scheduled_at key is included when dispatching the job
        $data = $request->all();
        $data['scheduled_at'] = $scheduledAt;
    
        SendScheduledMessage::dispatch($data, $userId)->delay($scheduledAt);
    
        $this->logMessage($request, $userId, 'scheduled', $scheduledAt);
    }
    

    public function getMessageLogs()
    {
        $messageLogs = MessageLog::with('user')->orderBy('created_at', 'desc')->get();

        // Ensure that scheduled_at is converted to a Carbon instance
        $messageLogs->each(function($log) {
            $log->scheduled_at = $log->scheduled_at ? Carbon::parse($log->scheduled_at) : null;
        });

        return view('admin.app-management', compact('messageLogs'));
    }
}
