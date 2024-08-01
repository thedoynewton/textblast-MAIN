<?php

namespace App\Http\Controllers;

use App\Services\MoviderService;
use Illuminate\Http\Request;
use App\Models\Student;

class MessageController extends Controller
{
    protected $moviderService;

    public function __construct(MoviderService $moviderService)
    {
        $this->moviderService = $moviderService;
    }

    public function sendBulkMessages(Request $request)
    {
        $query = Student::query();

        if ($request->filled('campus')) {
            $query->where('campus_id', $request->input('campus'));
        }

        if ($request->filled('college')) {
            $query->where('college_id', $request->input('college'));
        }

        if ($request->filled('program')) {
            $query->where('program_id', $request->input('program'));
        }

        if ($request->filled('year')) {
            $query->where('year_id', $request->input('year'));
        }

        $students = $query->get();
        $recipients = [];
        $invalidRecipients = [];

        foreach ($students as $student) {
            $number = $student->stud_contact;
            // Check if the number is already in the correct international format
            if (preg_match('/^\+639\d{9}$/', $number)) {
                $recipients[] = $number;
            } elseif (preg_match('/^09\d{9}$/', $number)) {
                // Convert the number to international format
                $recipients[] = '+63' . substr($number, 1);
            } else {
                $invalidRecipients[] = [
                    'name' => $student->stud_name,
                    'number' => $student->stud_contact,
                    'email' => $student->stud_email
                ];
            }
        }

        if (empty($recipients) && !empty($invalidRecipients)) {
            $errorMessage = 'The following numbers are invalid: ';
            foreach ($invalidRecipients as $invalidRecipient) {
                $errorMessage .= $invalidRecipient['name'] . ' (' . $invalidRecipient['number'] . ', ' . $invalidRecipient['email'] . '), ';
            }
            $errorMessage = rtrim($errorMessage, ', ');
            return redirect()->back()->with('error', $errorMessage);
        }

        $message = $request->input('message');
        $batchSize = 100; // Set batch size according to Movider API limits
        $recipientBatches = array_chunk($recipients, $batchSize);
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

        $successMessage = $successCount > 0 ? 'Messages sent successfully to ' . $successCount . ' recipients.' : '';
        $errorMessage = $errorCount > 0 ? 'Failed to send messages to ' . $errorCount . ' recipients.' : '';
        $errorDetails = !empty($invalidRecipients) ? ' The following numbers are invalid: ' : '';
        
        foreach ($invalidRecipients as $invalidRecipient) {
            $errorDetails .= $invalidRecipient['name'] . ' (' . $invalidRecipient['number'] . ', ' . $invalidRecipient['email'] . '), ';
        }

        $errorDetails = rtrim($errorDetails, ', ');

        if ($successCount > 0) {
            return redirect()->back()->with('success', $successMessage . $errorDetails);
        } else {
            return redirect()->back()->with('error', $errorMessage . $errorDetails . ' ' . implode(', ', $batchErrors));
        }
    }
}
