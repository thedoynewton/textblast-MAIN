<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Year;
use App\Models\Campus;
use App\Models\Office;
use App\Models\Status;
use App\Models\College;
use App\Models\Program;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Major;
use App\Models\MessageLog;
use App\Models\MessageRecipient;
use App\Models\MessageTemplate;
use App\Models\Type;
use App\Services\MoviderService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
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
            SUM(sent_count) AS total_recipients, -- Total sent recipients
            SUM(CASE WHEN schedule = 'scheduled' AND status = 'Sent' THEN sent_count ELSE 0 END) AS scheduled_sent_recipients,
            SUM(CASE WHEN schedule = 'immediate' AND status = 'Sent' THEN sent_count ELSE 0 END) AS immediate_sent_recipients,
            SUM(failed_count) AS total_failed_recipients,
            COUNT(CASE WHEN status = 'Cancelled' THEN 1 END) AS total_cancelled,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS total_pending,
            COUNT(CASE WHEN status = 'Scheduled' THEN 1 END) AS total_scheduled
        ")->first();
    
        // Set default values if stats are null
        $totalRecipients = $messageStats->total_recipients ?? 0; // Total recipients sent
        $scheduledSentRecipients = $messageStats->scheduled_sent_recipients ?? 0;
        $immediateSentRecipients = $messageStats->immediate_sent_recipients ?? 0;
        $totalFailedRecipients = $messageStats->total_failed_recipients ?? 0;
        $totalCancelled = $messageStats->total_cancelled ?? 0;
        $totalPending = $messageStats->total_pending ?? 0;
        $totalScheduled = $messageStats->total_scheduled ?? 0;
    
        // Fetch all message logs, including the associated user and campus data
        $messageLogs = MessageLog::with(['user', 'campus'])->orderBy('created_at', 'desc')->get();
    
        return view('admin.dashboard', compact(
            'balance',
            'totalRecipients',
            'scheduledSentRecipients',
            'immediateSentRecipients',
            'totalFailedRecipients',
            'totalCancelled',
            'totalPending',
            'totalScheduled',
            'messageLogs'
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

        return view('admin.messages', compact('campuses', 'colleges', 'programs', 'years', 'offices', 'statuses', 'types', 'majors', 'messageTemplates'));
    }


    public function broadcastMessages(Request $request)
    {
        return app(MessageController::class)->broadcastToRecipients($request);
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

        return view('admin.analytics', compact('balance', 'lowBalance', 'campuses', 'years', 'offices', 'statuses', 'types'));
    }

    public function userManagement()
    {
        $users = User::all();
        return view('admin.user-management', compact('users'));
    }

    public function appManagement()
    {
        // Fetch all necessary data
        $students = Student::all();
        $campuses = Campus::all();
        $colleges = College::all();
        $programs = Program::all();
        $majors = Major::all();
        $years = Year::all();
        $employees = Employee::all();
        $offices = Office::all();
        $statuses = Status::all();
        $types = Type::all();
        $messageTemplates = MessageTemplate::all();

        // Fetch the message logs
        $messageLogs = MessageLog::with('user')->orderBy('created_at', 'desc')->get();

        // Pass all the data to the view
        return view(
            'admin.app-management',
            compact(
                'students',
                'campuses',
                'colleges',
                'programs',
                'majors',
                'years',
                'employees',
                'offices',
                'statuses',
                'types',
                'messageTemplates',
                'messageLogs' // Pass the message logs to the view
            )
        );
    }

    // Method to update the contact number
    public function updateContactNumber(Request $request)
    {
        // Validate request data
        $validator = $request->validate([
            'email' => 'required|email',
            'contact_number' => 'required|string|max:15',  // Modify as per requirements
        ]);

        $email = $request->input('email');
        $newContactNumber = $request->input('contact_number');

        // Look for the recipient by email (Student or Employee)
        $recipient = Student::where('stud_email', $email)->first() ?? Employee::where('emp_email', $email)->first();

        // If no recipient is found, return an error
        if (!$recipient) {
            return response()->json(['success' => false, 'message' => 'Recipient not found.'], 404);
        }

        // Update the contact number based on whether the recipient is a Student or Employee
        if ($recipient instanceof Student) {
            $recipient->stud_contact = $newContactNumber;
        } else if ($recipient instanceof Employee) {
            $recipient->emp_contact = $newContactNumber;
        }

        // Save the updated contact number
        $recipient->save();

        return response()->json(['success' => true, 'message' => 'Contact number updated successfully.']);
    }

    public function importEmployees(Request $request)
    {
        return redirect()->route('admin.app-management')->with('success', 'Employees imported successfully.');
    }

    public function addUser(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                'regex:/^[a-zA-Z0-9._%+-]+@usep\.edu\.ph$/', // Ensure it's a valid @usep.edu.ph email
                'unique:users,email' // Ensure email doesn't already exist in the users table
            ],
        ]);

        // Check if the email exists in the Employee table
        $employee = Employee::where('emp_email', $request->email)->first();

        if (!$employee) {
            return redirect()->back()->withErrors(['email' => 'The email does not exist in the Employee records.']);
        }

        // Create the user if the email exists in Employee table
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.user-management')->with('success', 'User added successfully.');
    }

    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,subadmin',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.user-management')->with('success', 'User role updated successfully.');
    }

    public function removeAccess(User $user)
    {
        $user->role = null;
        $user->save();

        return redirect()->route('admin.user-management')->with('success', 'User access removed successfully.');
    }
}
