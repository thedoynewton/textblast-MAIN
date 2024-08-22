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
use Illuminate\Support\Facades\Log;

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

        $studentQuery = Student::query();
        $employeeQuery = Employee::query();

        if ($data['broadcast_type'] === 'students' || $data['broadcast_type'] === 'all') {
            if (isset($data['campus']) && $data['campus'] !== 'all') {
                $studentQuery->where('campus_id', $data['campus']);
            }
            if (isset($data['college']) && $data['college'] !== 'all') {
                $filterNames['college'] = College::find($data['college'])->college_name ?? 'All Colleges';
                $studentQuery->where('college_id', $data['college']);
            }
            if (isset($data['program']) && $data['program'] !== 'all') {
                $filterNames['program'] = Program::find($data['program'])->program_name ?? 'All Programs';
                $studentQuery->where('program_id', $data['program']);
            }
            if (isset($data['year']) && $data['year'] !== 'all') {
                $filterNames['year'] = Year::find($data['year'])->year_name ?? 'All Years';
                $studentQuery->where('year_id', $data['year']);
            }
        }

        if ($data['broadcast_type'] === 'employees' || $data['broadcast_type'] === 'all') {
            if (isset($data['campus']) && $data['campus'] !== 'all') {
                $employeeQuery->where('campus_id', $data['campus']);
            }
            if (isset($data['office']) && $data['office'] !== 'all') {
                $filterNames['office'] = Office::find($data['office'])->office_name ?? 'All Offices';
                $employeeQuery->where('office_id', $data['office']);
            }
            if (isset($data['status']) && $data['status'] !== 'all') {
                $filterNames['status'] = Status::find($data['status'])->status_name ?? 'All Statuses';
                $employeeQuery->where('status_id', $data['status']);
            }
            if (isset($data['type']) && $data['type'] !== 'all') {
                $filterNames['type'] = Type::find($data['type'])->type_name ?? 'All Types';
                $employeeQuery->where('type_id', $data['type']);
            }
        }

        // Calculate total recipients
        if ($data['broadcast_type'] === 'all') {
            $totalRecipients = $studentQuery->count() + $employeeQuery->count();
        } elseif ($data['broadcast_type'] === 'students') {
            $totalRecipients = $studentQuery->count();
        } else {
            $totalRecipients = $employeeQuery->count();
        }

        $data['schedule_type'] = $request->input('schedule', 'immediate');
        $data['scheduled_at'] = $request->input('scheduled_date');

        return view('admin.review-message', compact('data', 'campus', 'filterNames', 'totalRecipients'));
    }

    public function broadcastToRecipients(Request $request)
    {
        $broadcastType = $request->broadcast_type;
        $scheduleType = $request->schedule;
        $scheduledDate = $request->scheduled_date;
        $batchSize = $request->input('batch_size', 1); // Get batch size from the request
        $userId = Auth::id();

        if ($scheduleType === 'scheduled' && $scheduledDate) {
            $scheduledAt = Carbon::parse($scheduledDate);
            $this->scheduleMessage($request, $scheduledAt, $userId, $batchSize);

            return redirect()->route('admin.messages')->with('success', 'Message scheduled successfully.');
        } else {
            // Variables to track the counts
            $successCount = 0;
            $errorCount = 0;
            $totalRecipients = 0;
            $errorDetails = '';

            // Handle sending messages to students
            if ($broadcastType === 'students' || $broadcastType === 'all') {
                $studentResult = $this->sendBulkMessages($request, 'students', $batchSize);
                $successCount += $studentResult['successCount'];
                $errorCount += $studentResult['errorCount'];
                $totalRecipients += ($studentResult['successCount'] + $studentResult['errorCount']);
                $errorDetails .= (string) $studentResult['errorDetails'];
            }

            // Handle sending messages to employees
            if ($broadcastType === 'employees' || $broadcastType === 'all') {
                $employeeResult = $this->sendBulkMessages($request, 'employees', $batchSize);
                $successCount += $employeeResult['successCount'];
                $errorCount += $employeeResult['errorCount'];
                $totalRecipients += ($employeeResult['successCount'] + $employeeResult['errorCount']);
                $errorDetails .= (string) $employeeResult['errorDetails'];
            }

            // Calculate the percentage of successful deliveries
            $percentageSent = $totalRecipients > 0 ? ($successCount / $totalRecipients) * 100 : 0;

            // Log the message and update the message log
            $logId = $this->logMessage($request, $userId, 'immediate');
            if ($logId !== null) {
                $messageLog = MessageLog::find($logId);
                if ($messageLog) {
                    $messageLog->total_recipients = $totalRecipients;
                    $messageLog->sent_count = $successCount;
                    $messageLog->failed_count = $errorCount;
                    $messageLog->sent_at = now();
                    $messageLog->status = 'Sent';
                    $messageLog->save();
                }
            } else {
                // If logging failed, handle the error appropriately
                session()->flash('error', 'Message sending failed due to logging issues.');
                return redirect()->route('admin.messages');
            }

            // Store the log ID and the success message in the session
            session()->flash('logId', $logId);
            session()->flash('success', "Messages sent successfully to $successCount recipients out of $totalRecipients.");

            return redirect()->route('admin.messages');
        }
    }

    protected function sendMessageImmediately(Request $request, $userId)
    {
        $broadcastType = $request->broadcast_type;
        $successCount = 0;
        $errorCount = 0;
        $totalRecipients = 0;
        $errorDetails = '';
        $batchSize = $request->input('batch_size', 1); // Default to 1 if not set

        if ($broadcastType === 'students' || $broadcastType === 'all') {
            $studentResult = $this->sendBulkMessages($request, 'students', $batchSize);
            $successCount += $studentResult['successCount'];
            $errorCount += $studentResult['errorCount'];
            $totalRecipients += ($studentResult['successCount'] + $studentResult['errorCount']);
            $errorDetails .= (string) $studentResult['errorDetails'];
        }

        if ($broadcastType === 'employees' || $broadcastType === 'all') {
            $employeeResult = $this->sendBulkMessages($request, 'employees', $batchSize);
            $successCount += $employeeResult['successCount'];
            $errorCount += $employeeResult['errorCount'];
            $totalRecipients += ($employeeResult['successCount'] + $employeeResult['errorCount']);
            $errorDetails .= (string) $employeeResult['errorDetails'];
        }

        // Calculate the progress percentage
        $progress = $totalRecipients > 0 ? ($successCount / $totalRecipients) * 100 : 0;

        // Store the progress in the session
        session()->flash('progress', $progress);

        // Attempt to log the message
        $logId = $this->logMessage($request, $userId, 'immediate');

        if ($logId === null) {
            session()->flash('error', 'Message sending failed due to logging issues.');
            return redirect()->route('admin.messages');
        }

        // Update the message log with the calculated values
        $messageLog = MessageLog::find($logId);
        if ($messageLog) {
            $messageLog->total_recipients = $totalRecipients;
            $messageLog->sent_count = $successCount;
            $messageLog->failed_count = $errorCount;
            $messageLog->sent_at = now();
            $messageLog->status = 'Sent';
            $messageLog->save();
        }

        if ($successCount > 0) {
            session()->flash('success', "Messages sent successfully to $successCount recipients." . $errorDetails);
        } else {
            session()->flash('error', "Failed to send messages to $errorCount recipients." . $errorDetails);
        }

        return redirect()->route('admin.messages');
    }

    protected function sendBulkMessages(Request $request, $recipientType, $batchSize)
    {
        $query = $this->buildRecipientQuery($request, $recipientType);
        $recipients = $query->get();
        $formattedRecipients = [];
        $invalidRecipients = [];
        $errorDetails = '';

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

        if (empty($formattedRecipients)) {
            return [
                'successCount' => 0,
                'errorCount' => count($invalidRecipients),
                'errorDetails' => 'All numbers are invalid.'
            ];
        }

        $message = $request->input('message');
        $successCount = 0;
        $errorCount = 0;
        $batchErrors = [];

        foreach (array_chunk($formattedRecipients, $batchSize) as $batch) {
            $response = $this->moviderService->sendBulkSMS($batch, $message);
            if (isset($response->phone_number_list)) {
                $successCount += count($response->phone_number_list);
            } else {
                $errorCount += count($batch);
                $batchErrors[] = $response->error->description ?? 'Unknown error';
            }
        }

        if (!empty($invalidRecipients)) {
            $errorDetails .= ' Invalid numbers: ' . implode(', ', array_map(function ($recipient) {
                return $recipient['name'] . ' (' . $recipient['number'] . ')';
            }, $invalidRecipients)) . '.';
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

    protected function buildRecipientQuery(Request $request, $type)
    {
        $query = $type === 'students' ? Student::query() : Employee::query();

        if ($request->filled('campus') && $request->input('campus') !== 'all') {
            $query->where('campus_id', $request->input('campus'));
        }

        if ($type === 'students') {
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

        return $query;
    }

    protected function logMessage(Request $request, $userId, $scheduleType, $scheduledAt = null)
    {
        try {
            $status = $scheduleType === 'immediate' ? 'Sent' : 'Pending';

            $log = MessageLog::create([
                'user_id' => $userId,
                'recipient_type' => $request->broadcast_type,
                'content' => $request->message,
                'schedule' => $scheduleType === 'scheduled' && $scheduledAt ? 'scheduled' : 'immediate',
                'scheduled_at' => $scheduledAt,
                'sent_at' => $scheduleType === 'immediate' ? now() : null,
                'status' => $status,
            ]);

            return $log->id;
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating message log: ' . $e->getMessage());

            // Optionally, notify the user (if appropriate for your application)
            session()->flash('error', 'There was an issue logging the message. Please try again.');

            // Return null or handle it as needed in the calling method
            return null;
        }
    }

    protected function updateMessageLogStatus($logId, $status)
    {
        $messageLog = MessageLog::find($logId);
        if ($messageLog) {
            $messageLog->sent_at = now();
            $messageLog->status = $status;
            $messageLog->save();
        }
    }

    protected function scheduleMessage(Request $request, Carbon $scheduledAt, $userId, $batchSize)
    {
        $data = $request->all();
        $data['scheduled_at'] = $scheduledAt;
        $data['batch_size'] = $batchSize; // Include batch size in the data

        // Calculate the total recipients when scheduling the message
        $totalRecipients = 0;
        if ($request->broadcast_type === 'students' || $request->broadcast_type === 'all') {
            $studentQuery = $this->buildRecipientQuery($request, 'students');
            $totalRecipients += $studentQuery->count();
        }
        if ($request->broadcast_type === 'employees' || $request->broadcast_type === 'all') {
            $employeeQuery = $this->buildRecipientQuery($request, 'employees');
            $totalRecipients += $employeeQuery->count();
        }

        // Log the message with total recipients
        $logId = $this->logMessage($request, $userId, 'scheduled', $scheduledAt);
        $messageLog = MessageLog::find($logId);
        $messageLog->total_recipients = $totalRecipients;
        $messageLog->save();

        // Pass the log ID and batch size to the job
        $data['log_id'] = $logId;

        SendScheduledMessage::dispatch($data, $userId)->delay($scheduledAt);
    }

    public function getMessageLogs()
    {
        $messageLogs = MessageLog::with('user')->orderBy('created_at', 'desc')->get();

        $messageLogs->each(function ($log) {
            $log->scheduled_at = $log->scheduled_at ? Carbon::parse($log->scheduled_at) : null;
        });

        return view('admin.app-management', compact('messageLogs'));
    }

    public function getRecipientCount(Request $request)
    {
        $broadcastType = $request->query('broadcast_type');
        $campusId = $request->query('campus_id');
        $collegeId = $request->query('college_id');
        $programId = $request->query('program_id');
        $yearId = $request->query('year_id');
        $officeId = $request->query('office_id');
        $statusId = $request->query('status_id');
        $typeId = $request->query('type_id');

        $studentQuery = Student::query();
        $employeeQuery = Employee::query();

        if ($campusId && $campusId !== 'all') {
            $studentQuery->where('campus_id', $campusId);
            $employeeQuery->where('campus_id', $campusId);
        }

        if ($broadcastType === 'students' || $broadcastType === 'all') {
            if ($collegeId && $collegeId !== 'all') {
                $studentQuery->where('college_id', $collegeId);
            }

            if ($programId && $programId !== 'all') {
                $studentQuery->where('program_id', $programId);
            }

            if ($yearId && $yearId !== 'all') {
                $studentQuery->where('year_id', $yearId);
            }
        }

        if ($broadcastType === 'employees' || $broadcastType === 'all') {
            if ($officeId && $officeId !== 'all') {
                $employeeQuery->where('office_id', $officeId);
            }

            if ($statusId && $statusId !== 'all') {
                $employeeQuery->where('status_id', $statusId);
            }

            if ($typeId && $typeId !== 'all') {
                $employeeQuery->where('type_id', $typeId);
            }
        }

        if ($broadcastType === 'all') {
            $total = $studentQuery->count() + $employeeQuery->count();
        } elseif ($broadcastType === 'students') {
            $total = $studentQuery->count();
        } else {
            $total = $employeeQuery->count();
        }

        return response()->json(['total' => $total ?: 0]); // Return 0 if no recipients are found
    }

    public function getProgress($logId)
    {
        $log = MessageLog::find($logId);
        if ($log) {
            $totalRecipients = $log->total_recipients;
            $sentCount = $log->sent_count;
            $failedCount = $log->failed_count;
            $percentageSent = $totalRecipients > 0 ? ($sentCount + $failedCount) / $totalRecipients * 100 : 0;

            return response()->json([
                'percentageSent' => round($percentageSent, 2),
                'sentCount' => $sentCount,
                'failedCount' => $failedCount,
                'totalRecipients' => $totalRecipients,
            ]);
        }

        return response()->json([
            'percentageSent' => 0,
            'sentCount' => 0,
            'failedCount' => 0,
            'totalRecipients' => 0,
        ]);
    }
}
