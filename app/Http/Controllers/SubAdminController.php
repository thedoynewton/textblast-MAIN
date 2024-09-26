<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Campus;
use App\Models\Office;
use App\Models\Status;
use App\Models\College;
use App\Models\Major;
use App\Models\MessageLog;
use App\Models\MessageRecipient;
use App\Models\MessageTemplate;
use App\Models\Program;
use App\Models\Type;
use App\Services\MoviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubAdminController extends Controller
{

    public function getImmediateRecipients()
    {
        $recipients = MessageRecipient::whereHas('messageLog', function ($query) {
            $query->where('schedule', 'immediate');
        })->get(['first_name', 'last_name', 'email', 'contact_number']);
    
        return response()->json($recipients);
    }

    public function getFailedRecipients()
    {
        $recipients = MessageRecipient::where('sent_status', 'Failed')
            ->get(['first_name', 'last_name', 'email', 'contact_number', 'failure_reason']);
        return response()->json($recipients);
    }

    public function getScheduledMessageRecipients()
    {
        $recipients = MessageRecipient::whereHas('messageLog', function ($query) {
            $query->where('schedule', 'scheduled')->where('status', 'Sent');
        })->get();
        return response()->json($recipients);
    }

    public function dashboard(MoviderService $moviderService)
    {
        // Get balance using Movider Service
        $balanceData = $moviderService->getBalance();
        $balance = $balanceData['balance'] ?? 0;

        // Query MessageLog table only once and aggregate counts
        $messageStats = MessageLog::selectRaw("
            COUNT(CASE WHEN status = 'Sent' THEN 1 END) AS total_sent,
            COUNT(CASE WHEN schedule = 'scheduled' AND status = 'Sent' THEN 1 END) AS scheduled_sent,
            COUNT(CASE WHEN failed_count > 0 THEN 1 END) AS total_failed,
            COUNT(CASE WHEN schedule = 'immediate' AND status = 'Sent' THEN 1 END) AS total_immediate,
            COUNT(CASE WHEN status = 'Cancelled' THEN 1 END) AS total_cancelled,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS total_pending,
            COUNT(CASE WHEN status = 'Scheduled' THEN 1 END) AS total_scheduled
        ")->first();

        // Set default values if stats are null
        $totalSent = $messageStats->total_sent ?? 0;
        $scheduledSent = $messageStats->scheduled_sent ?? 0;
        $totalFailed = $messageStats->total_failed ?? 0;
        $totalImmediate = $messageStats->total_immediate ?? 0;
        $totalCancelled = $messageStats->total_cancelled ?? 0;
        $totalPending = $messageStats->total_pending ?? 0;
        $totalScheduled = $messageStats->total_scheduled ?? 0;

        // Fetch all message logs, including the associated user and campus data
        $messageLogs = MessageLog::with(['user', 'campus'])->orderBy('created_at', 'desc')->get();

        return view('subadmin.dashboard', compact(
            'balance',
            'totalSent',
            'scheduledSent',
            'totalFailed',
            'totalImmediate',
            'totalCancelled',
            'totalPending',
            'totalScheduled',
            'messageLogs' // Pass the message logs to the view
        ));
    }


    public function messages()
    {
        $campuses = Campus::all();
        $colleges = College::all();
        $programs = Program::all();
        $years = Year::all();
        $offices = Office::all();
        $statuses = Status::all();
        $types = Type::all();
        $majors = Major::all(); // Fetch all majors
        $messageTemplates = MessageTemplate::all();
    
        return view('subadmin.messages', compact('campuses', 'colleges', 'programs', 'years', 'offices', 'statuses', 'types', 'majors', 'messageTemplates'));
    }

    public function broadcastMessages(Request $request)
    {
        // Delegate the broadcasting logic to MessageController
        return app(MessageController::class)->broadcastToRecipients($request);
    }

    public function analytics(MoviderService $moviderService)
    {
        $balanceData = $moviderService->getBalance();
        $balance = $balanceData['balance'] ?? 0;

        // Fetch campuses, years, offices, statuses, and types from the database
        $campuses = Campus::all();
        $years = Year::all();
        $offices = Office::all();
        $statuses = Status::all();
        $types = Type::all();

        // Set the threshold for low balance
        $warningThreshold = 0.065; // Adjust as needed

        // Check if the balance is low
        $lowBalance = $balance < $warningThreshold;

        // Log the balance value
        Log::info('Movider Balance:', ['balance' => $balance]);

        return view('subadmin.analytics', compact('balance', 'lowBalance', 'campuses', 'years', 'offices', 'statuses', 'types'));
    }
    
    protected function sendMoviderMessage($phoneNumber, $message)
    {
        $apiKey = config('services.movider.api_key');
        $apiSecret = config('services.movider.api_secret');

        Log::info('Movider API Key: ' . $apiKey);
        Log::info('Movider API Secret: ' . $apiSecret);

        $response = Http::post('https://api.movider.co/v1/sms', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'to' => $phoneNumber,
            'text' => $message,
        ]);

        Log::info('Movider API Response: ', $response->json());

        return $response;
    }
}
