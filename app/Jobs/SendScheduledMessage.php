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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendScheduledMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $userId;

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

            // No need to log here since it's already logged during scheduling
            // $this->logMessage('scheduled', now());
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
                        Log::info("Message sent successfully to: {$formattedNumber}");
                    } else {
                        Log::error("Failed to send message to: {$formattedNumber} - Error: " . ($response->error->description ?? 'Unknown error'));
                    }
                } catch (\Exception $e) {
                    Log::error("Exception sending message to: {$formattedNumber} - " . $e->getMessage());
                }
            } else {
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

    protected function logMessage($scheduleType, $sentAt)
    {
        $sentAt = Carbon::parse($sentAt)->timezone(config('app.timezone'));

        try {
            MessageLog::create([
                'user_id' => $this->userId,
                'recipient_type' => $this->data['broadcast_type'],
                'content' => $this->data['message'],
                'schedule' => $scheduleType,
                'scheduled_at' => isset($this->data['scheduled_at']) ? Carbon::parse($this->data['scheduled_at'])->timezone(config('app.timezone')) : null,
                'sent_at' => $sentAt,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Error logging message: " . $e->getMessage());
        }
    }
}
