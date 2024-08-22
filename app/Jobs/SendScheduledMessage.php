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

            $number = preg_replace('/\D/', '', $number);
            $number = substr($number, -10);

            if (strlen($number) === 10) {
                $formattedNumber = '+63' . $number;

                // Send the message individually
                try {
                    $response = $moviderService->sendBulkSMS([$formattedNumber], $this->data['message']);

                    if (isset($response->phone_number_list) && !empty($response->phone_number_list)) {
                        $this->successCount += count($response->phone_number_list);
                        Log::info("Message sent successfully to: {$formattedNumber}");
                    } else {
                        $this->failedCount++;
                        Log::error("Failed to send message to: {$formattedNumber} - Error: " . ($response->error->description ?? 'Unknown error'));
                    }
                } catch (\Exception $e) {
                    $this->failedCount++;
                    Log::error("Exception sending message to: {$formattedNumber} - " . $e->getMessage());
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

        if ($messageLog) {
            $messageLog->sent_at = now(); // Update the sent_at timestamp
            $messageLog->status = 'Sent'; // Update the status to 'Sent'
            $messageLog->total_recipients = $this->totalRecipients; // Set the total recipients
            $messageLog->sent_count = $this->successCount; // Set the number of successful deliveries
            $messageLog->failed_count = $this->failedCount; // Set the number of failed messages
            $messageLog->save();
        } else {
            Log::error('MessageLog not found for ID: ' . $this->data['log_id']);
        }
    }
}
