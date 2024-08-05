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
use Illuminate\Http\Request;
use App\Services\MoviderService;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendScheduledMessage;
use Carbon\Carbon;

class MessageController extends Controller
{
    protected $moviderService;

    public function __construct(MoviderService $moviderService)
    {
        $this->moviderService = $moviderService;
    }

    /**
     * Broadcast messages to either students, employees, or both.
     */
    public function broadcastToRecipients(Request $request)
    {
        $broadcastType = $request->broadcast_type;
        $campusId = $request->input('campus');
        $successCount = 0;
        $errorCount = 0;
        $errorDetails = '';

        // Handle broadcasting to students
        if ($broadcastType === 'students' || $broadcastType === 'all') {
            $studentResult = $this->sendBulkMessages($request, 'students', $campusId);
            $successCount += $studentResult['successCount'];
            $errorCount += $studentResult['errorCount'];
            $errorDetails .= $studentResult['errorDetails'];
        }

        // Handle broadcasting to employees
        if ($broadcastType === 'employees' || $broadcastType === 'all') {
            $employeeResult = $this->sendBulkMessages($request, 'employees', $campusId);
            $successCount += $employeeResult['successCount'];
            $errorCount += $employeeResult['errorCount'];
            $errorDetails .= $employeeResult['errorDetails'];
        }

        $successMessage = $successCount > 0 ? "Messages sent successfully to $successCount recipients." : '';
        $errorMessage = $errorCount > 0 ? "Failed to send messages to $errorCount recipients." : '';

        if ($successCount > 0) {
            return redirect()->back()->with('success', $successMessage . $errorDetails);
        } else {
            return redirect()->back()->with('error', $errorMessage . $errorDetails);
        }
    }

    /**
     * Sends bulk messages to the specified recipient type (students or employees).
     */
    protected function sendBulkMessages(Request $request, $recipientType, $campusId = null)
    {
        $query = $recipientType === 'students' ? Student::query() : Employee::query();

        if ($campusId) {
            $query->where('campus_id', $campusId);
        }

        if ($recipientType === 'students') {
            if ($request->filled('college')) {
                $query->where('college_id', $request->input('college'));
            }

            if ($request->filled('program')) {
                $query->where('program_id', $request->input('program'));
            }

            if ($request->filled('year')) {
                $query->where('year_id', $request->input('year'));
            }
        } else { // employees
            if ($request->filled('office')) {
                $query->where('office_id', $request->input('office'));
            }

            if ($request->filled('status')) {
                $query->where('status_id', $request->input('status'));
            }

            if ($request->filled('type')) {
                $query->where('type_id', $request->input('type'));
            }
        }

        $recipients = $query->get();
        $formattedRecipients = [];
        $invalidRecipients = [];

        foreach ($recipients as $recipient) {
            $number = $recipientType === 'students' ? $recipient->stud_contact : $recipient->phone;
            $email = $recipientType === 'students' ? $recipient->stud_email : $recipient->email;

            if (preg_match('/^\+639\d{9}$/', $number)) {
                $formattedRecipients[] = $number;
            } elseif (preg_match('/^09\d{9}$/', $number)) {
                $formattedRecipients[] = '+63' . substr($number, 1);
            } else {
                $invalidRecipients[] = [
                    'name' => $recipientType === 'students' ? $recipient->stud_name : $recipient->name,
                    'number' => $number,
                    'email' => $email
                ];
            }
        }

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

        $message = $request->input('message');
        $batchSize = 100; // Adjust this number based on Movider's API limits
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

    /**
     * Schedule a message to be sent at a later time.
     */
    public function scheduleMessage(Request $request)
    {
        $recipients = $this->getRecipients($request);
        $message = $request->input('message');
        $scheduledTime = Carbon::parse($request->input('scheduled_time'));

        // Dispatch the job with a delay until the scheduled time
        SendScheduledMessage::dispatch($recipients, $message)
                            ->delay($scheduledTime);

        return back()->with('success', 'Message scheduled successfully for ' . $scheduledTime->toDayDateTimeString());
    }

    /**
     * Get recipients based on the selected filters.
     */
    protected function getRecipients(Request $request)
    {
        $broadcastType = $request->input('broadcast_type');
        $recipients = [];

        if ($broadcastType === 'students' || $broadcastType === 'all') {
            $recipients = Student::query()
                ->where('campus_id', $request->input('campus'))
                // Additional student filters here
                ->pluck('stud_contact')
                ->toArray();
        }

        if ($broadcastType === 'employees' || $broadcastType === 'all') {
            $recipients = array_merge($recipients, Employee::query()
                ->where('campus_id', $request->input('campus'))
                // Additional employee filters here
                ->pluck('phone')
                ->toArray());
        }

        return $recipients;
    }
}
