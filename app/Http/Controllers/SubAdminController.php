<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Campus;
use App\Models\Office;
use App\Models\Status;
use App\Models\College;
use App\Models\MessageTemplate;
use App\Models\Program;
use App\Models\Type;
use App\Services\MoviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubAdminController extends Controller
{
    public function dashboard()
    {
        return view('subadmin.dashboard');
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
        $messageTemplates = MessageTemplate::all(); // Add this line
    
        return view('subadmin.messages', compact('campuses', 'colleges', 'programs', 'years', 'offices', 'statuses', 'types', 'messageTemplates'));
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
    
        // Set the threshold for low balance
        $warningThreshold = 0.065; // Adjust as needed
    
        // Check if the balance is low
        $lowBalance = $balance < $warningThreshold;
    
        return view('subadmin.analytics', compact('balance', 'lowBalance'));
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
