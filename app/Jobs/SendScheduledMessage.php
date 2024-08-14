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

    // Send the message to the recipients based on the broadcast type
    if ($broadcastType === 'students' || $broadcastType === 'all') {
        $this->sendBulkMessages($moviderService, 'students');
    }

    if ($broadcastType === 'employees' || $broadcastType === 'all') {
        $this->sendBulkMessages($moviderService, 'employees');
    }

    // Log the sent message with the correct sent time
    $this->logMessage('scheduled', now());
}


    protected function sendBulkMessages(MoviderService $moviderService, $recipientType)
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
            Log::warning('The following numbers are invalid:', $invalidRecipients);
            return;
        }

        $message = $this->data['message'];
        $batchSize = 100;
        $recipientBatches = array_chunk($formattedRecipients, $batchSize);

        foreach ($recipientBatches as $batch) {
            $response = $moviderService->sendBulkSMS($batch, $message);

            if (isset($response->phone_number_list) && !empty($response->phone_number_list)) {
                Log::info('Messages sent successfully to the following numbers:', $response->phone_number_list);
            } else {
                Log::error('Failed to send messages to the following batch:', $batch);
            }
        }
    }

    protected function logMessage($scheduleType, $sentAt)
{
    $sentAt = Carbon::parse($sentAt)->timezone(config('app.timezone'));
    
    MessageLog::create([
        'user_id' => $this->userId,
        'recipient_type' => $this->data['broadcast_type'],
        'content' => $this->data['message'],
        'schedule' => $scheduleType,
        'scheduled_at' => Carbon::parse($this->data['scheduled_at'])->timezone(config('app.timezone')),
        'sent_at' => $sentAt,
        'created_at' => now(),
    ]);
}


}
