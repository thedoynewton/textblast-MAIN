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

    public function broadcastToRecipients(Request $request)
    {
        $timing = $request->input('timing');
        $recipients = $this->getRecipients($request);

        if ($timing === 'realtime') {
            // Send the message immediately
            return $this->sendBulkMessages($request, $recipients);
        } elseif ($timing === 'scheduled') {
            // Schedule the message
            return $this->scheduleMessage($request, $recipients);
        } else {
            return back()->with('error', 'Invalid timing option selected.');
        }
    }


    protected function sendBulkMessages(Request $request, $recipients)
    {
        $formattedRecipients = [];
        $invalidRecipients = [];

        foreach ($recipients as $recipient) {
            $number = $recipient['contact'];

            if (preg_match('/^\+639\d{9}$/', $number)) {
                $formattedRecipients[] = $number;
            } elseif (preg_match('/^09\d{9}$/', $number)) {
                $formattedRecipients[] = '+63' . substr($number, 1);
            } else {
                $invalidRecipients[] = $recipient;
            }
        }

        if (empty($formattedRecipients) && !empty($invalidRecipients)) {
            $errorMessage = 'The following numbers are invalid: ';
            foreach ($invalidRecipients as $invalidRecipient) {
                $errorMessage .= $invalidRecipient['name'] . ' (Number: ' . $invalidRecipient['contact'] . ', Email: ' . $invalidRecipient['email'] . '), ';
            }
            $errorMessage = rtrim($errorMessage, ', ');
            return back()->with('error', $errorMessage);
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

        $successMessage = $successCount > 0 ? "Messages sent successfully to $successCount recipients." : '';
        $errorMessage = $errorCount > 0 ? "Failed to send messages to $errorCount recipients." : '';
        $errorDetails = !empty($batchErrors) ? implode(', ', $batchErrors) : '';

        return back()->with('success', $successMessage)->with('error', $errorMessage . $errorDetails);
    }

    public function scheduleMessage(Request $request, $recipients)
    {
        $scheduledTime = Carbon::parse($request->input('scheduled_time'))->setTimezone('Asia/Manila');

        // Log the scheduled time and recipients
        Log::info('Scheduling Message', [
            'scheduled_time' => $scheduledTime,
            'recipients' => $recipients,
            'message' => $request->input('message')
        ]);

        SendScheduledMessage::dispatch($recipients, $request->input('message'))
            ->delay($scheduledTime);

        return back()->with('success', 'Message scheduled successfully for ' . $scheduledTime->toDayDateTimeString());
    }


    protected function getRecipients(Request $request)
    {
        $broadcastType = $request->input('broadcast_type');
        $recipients = [];

        if ($broadcastType === 'students' || $broadcastType === 'all') {
            $recipients = Student::query()
                ->where('campus_id', $request->input('campus'))
                ->select(['stud_contact as contact', 'stud_fname as name', 'stud_email as email'])
                ->get()
                ->toArray();
        }

        if ($broadcastType === 'employees' || $broadcastType === 'all') {
            $employeeRecipients = Employee::query()
                ->where('campus_id', $request->input('campus'))
                ->select(['emp_contact as contact', 'emp_fname as name', 'emp_email as email'])
                ->get()
                ->map(function ($item) {
                    return [
                        'contact' => $item->contact,
                        'name' => $item->name,
                        'email' => $item->email,
                    ];
                })
                ->toArray();

            $recipients = array_merge($recipients, $employeeRecipients);
        }

        return $recipients;
    }
}
