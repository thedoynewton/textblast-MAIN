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
        $successCount = 0;
        $errorCount = 0;
        $errorDetails = ''; // Initialize as an empty string

        // Handle broadcasting to students
        if ($broadcastType === 'students' || $broadcastType === 'both') {
            $studentResult = $this->sendBulkMessages($request, 'students');
            $successCount += $studentResult['successCount'];
            $errorCount += $studentResult['errorCount'];
            $errorDetails .= (string) $studentResult['errorDetails']; // Cast to string
        }

        // Handle broadcasting to employees
        if ($broadcastType === 'employees' || $broadcastType === 'both') {
            $employeeResult = $this->sendBulkMessages($request, 'employees');
            $successCount += $employeeResult['successCount'];
            $errorCount += $employeeResult['errorCount'];
            $errorDetails .= (string) $employeeResult['errorDetails']; // Cast to string
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
    protected function sendBulkMessages(Request $request, $recipientType)
    {
        $query = $recipientType === 'students' ? Student::query() : Employee::query();

        if ($request->filled('campus')) {
            $query->where('campus_id', $request->input('campus'));
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

}
