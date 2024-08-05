<?php

namespace App\Jobs;

use App\Services\MoviderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipients;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param array $recipients
     * @param string $message
     * @return void
     */
    public function __construct(array $recipients, string $message)
    {
        $this->recipients = $recipients;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MoviderService $moviderService)
    {
        $moviderService->sendBulkSMS($this->recipients, $this->message);
    }
}

