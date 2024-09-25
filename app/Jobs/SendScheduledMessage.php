<?php

namespace App\Jobs;

use App\Services\MoviderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MessageLog;
use App\Models\Student;
use App\Models\Employee;
use App\Models\MessageRecipient;
use Illuminate\Support\Facades\Log;

class SendScheduledMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $userId;

    protected $totalRecipients = 0;
    protected $successCount = 0;
    protected $failedCount = 0;

    public function __construct($data, $userId)
    {
        $this->data = $data;
        $this->userId = $userId;
    }

    public function handle(MoviderService $moviderService)
    {
        // Fetch the message log to check its status before proceeding
        $messageLog = MessageLog::find($this->data['log_id']);

        if (!$messageLog) {
            Log::error("MessageLog not found for ID: {$this->data['log_id']}");
            return;
        }

        if ($messageLog->status === 'Cancelled') {
            Log::info("Scheduled message [ID: {$this->data['log_id']}] has been cancelled. Aborting sending process.");
            return;
        }

        $broadcastType = $this->data['broadcast_type'];

        try {
            // Send the message to the recipients based on the broadcast type
            if ($broadcastType === 'students' || $broadcastType === 'all') {
                $this->sendIndividualMessages($moviderService, 'students');
            }

            if ($broadcastType === 'employees' || $broadcastType === 'all') {
                $this->sendIndividualMessages($moviderService, 'employees');
            }

            // After sending messages, update the message log with the final counts
            $this->updateMessageLogStatus();
        } catch (\Exception $e) {
            Log::error('Error sending scheduled message: ' . $e->getMessage());
        }
    }

    protected function sendIndividualMessages(MoviderService $moviderService, $recipientType)
    {
        $query = $recipientType === 'students' ? Student::query() : Employee::query();

        if (isset($this->data['campus']) && $this->data['campus'] !== 'all') {
            $query->where('campus_id', $this->data['campus']);
        }

        if ($recipientType === 'students') {
            if (isset($this->data['college']) && $this->data['college'] !== 'all') {
                $query->where('college_id', $this->data['college']);
            }

            if (isset($this->data['program']) && $this->data['program'] !== 'all') {
                $query->where('program_id', $this->data['program']);
            }

            if (isset($this->data['major']) && $this->data['major'] !== 'all') {
                $query->where('major_id', $this->data['major']);
            }

            if (isset($this->data['year']) && $this->data['year'] !== 'all') {
                $query->where('year_id', $this->data['year']);
            }
        } else {
            if (isset($this->data['office']) && $this->data['office'] !== 'all') {
                $query->where('office_id', $this->data['office']);
            }

            if (isset($this->data['status']) && $this->data['status'] !== 'all') {
                $query->where('status_id', $this->data['status']);
            }

            if (isset($this->data['type']) && $this->data['type'] !== 'all') {
                $query->where('type_id', $this->data['type']);
            }
        }

        $recipients = $query->get();
        $this->totalRecipients += $recipients->count();
        $invalidRecipients = [];

        foreach ($recipients as $recipient) {
            $number = $recipientType === 'students' ? $recipient->stud_contact : $recipient->emp_contact;

            // Check if the stud_id or emp_id is present
            Log::info("Processing recipient", [
                'recipient_type' => $recipientType,
                'stud_id' => $recipient->stud_id ?? 'N/A',
                'emp_id' => $recipient->emp_id ?? 'N/A',
                'first_name' => $recipient->stud_fname ?? $recipient->emp_fname,
                'last_name' => $recipient->stud_lname ?? $recipient->emp_lname,
            ]);

            $number = preg_replace('/\D/', '', $number);
            $number = substr($number, -10);

            if (strlen($number) === 10) {
                $formattedNumber = '+63' . $number;

                // Insert recipient into message_recipients table
                try {
                    $messageRecipient = MessageRecipient::create([
                        'message_log_id' => $this->data['log_id'],
                        'recipient_type' => $recipientType === 'students' ? 'student' : 'employee',
                        'stud_id' => $recipientType === 'students' ? $recipient->stud_id : null,
                        'emp_id' => $recipientType === 'employees' ? $recipient->emp_id : null,
                        'first_name' => $recipient->stud_fname ?? $recipient->emp_fname,
                        'last_name' => $recipient->stud_lname ?? $recipient->emp_lname,
                        'middle_name' => $recipient->stud_mname ?? $recipient->emp_mname,
                        'contact_number' => '09' . substr($number, -9),
                        'email' => $recipient->stud_email ?? $recipient->emp_email,
                        'campus_id' => $recipient->campus_id,
                        'college_id' => $recipientType === 'students' ? $recipient->college_id : null,
                        'program_id' => $recipientType === 'students' ? $recipient->program_id : null,
                        'major_id' => $recipientType === 'students' ? $recipient->major_id : null,
                        'year_id' => $recipientType === 'students' ? $recipient->year_id : null,
                        'enrollment_stat' => $recipientType === 'students' ? $recipient->enrollment_stat : null,
                        'office_id' => $recipientType === 'employees' ? $recipient->office_id : null,
                        'status_id' => $recipientType === 'employees' ? $recipient->status_id : null,
                        'type_id' => $recipientType === 'employees' ? $recipient->type_id : null,
                        'sent_status' => 'Failed', // Default to Failed, will update to Sent if successful
                    ]);

                    Log::info("Recipient successfully logged in message_recipients table", [
                        'message_log_id' => $this->data['log_id'],
                        'recipient_type' => $recipientType,
                        'stud_id' => $messageRecipient->stud_id,
                        'emp_id' => $messageRecipient->emp_id,
                        'contact_number' => $formattedNumber,
                    ]);
                    
                    // Send the message individually
                    $response = $moviderService->sendBulkSMS([$formattedNumber], $this->data['message']);
                    if (isset($response->phone_number_list) && !empty($response->phone_number_list)) {
                        $this->successCount += count($response->phone_number_list);
                        Log::info("Message sent successfully to: {$formattedNumber}");

                        // Update sent_status to 'Sent' for successful messages
                        $messageRecipient->update(['sent_status' => 'Sent']);
                    } else {
                        $this->failedCount++;
                        Log::error("Failed to send message to: {$formattedNumber} - Error: " . ($response->error->description ?? 'Unknown error'));
                    }
                } catch (\Exception $e) {
                    $this->failedCount++;
                    Log::error("Error logging recipient in message_recipients table: " . $e->getMessage(), [
                        'recipient_type' => $recipientType,
                        'contact_number' => $formattedNumber,
                        'message_log_id' => $this->data['log_id'],
                    ]);
                }
            } else {
                $this->failedCount++;
                $invalidRecipients[] = [
                    'name' => $recipientType === 'students' ? $recipient->stud_name : $recipient->emp_name,
                    'number' => $number,
                ];
            }
        }

        if (!empty($invalidRecipients)) {
            Log::warning('The following numbers are invalid:', $invalidRecipients);
        }
    }

    protected function updateMessageLogStatus()
    {
        $messageLog = MessageLog::where('id', $this->data['log_id'])->first();

        if ($messageLog && $messageLog->status !== 'Cancelled') {
            $messageLog->sent_at = now(); // Update the sent_at timestamp
            $messageLog->status = 'Sent'; // Update the status to 'Sent'
            $messageLog->total_recipients = $this->totalRecipients; // Set the total recipients
            $messageLog->sent_count = $this->successCount; // Set the number of successful deliveries
            $messageLog->failed_count = $this->failedCount; // Set the number of failed messages
            $messageLog->save();
        } elseif ($messageLog->status === 'Cancelled') {
            Log::info("Message [ID: {$messageLog->id}] was cancelled. Not updating status to Sent.");
        } else {
            Log::error('MessageLog not found for ID: ' . $this->data['log_id']);
        }
    }
}
