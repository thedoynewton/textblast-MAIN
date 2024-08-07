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
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
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

        // Convert the collections to arrays for better visibility
        return view('admin.messages', compact('campuses', 'colleges', 'programs', 'years', 'offices', 'statuses', 'types'));
    }

    public function broadcastMessages(Request $request)
    {
        return app(MessageController::class)->broadcastToRecipients($request);
    }

    protected function sendMoviderMessage($phoneNumber, $message)
    {
        $apiKey = config('services.movider.api_key');
        $apiSecret = config('services.movider.api_secret');

        // Log the API key and secret for debugging purposes (do not do this in production)
        Log::info('Movider API Key: ' . $apiKey);
        Log::info('Movider API Secret: ' . $apiSecret);

        $response = Http::post('https://api.movider.co/v1/sms', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'to' => $phoneNumber,
            'text' => $message,
        ]);

        // Log the response for debugging
        Log::info('Movider API Response: ', $response->json());

        return $response;
    }

    public function analytics()
    {
        return view('admin.analytics');
    }

    public function userManagement()
    {
        $users = User::all();
        return view('admin.user-management', compact('users'));
    }

    public function appManagement()
    {
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

        return view('admin.app-management', compact(
            'students',
            'campuses',
            'colleges',
            'programs',
            'majors',
            'years',
            'employees',
            'offices',
            'statuses',
            'types'
        ));
    }


    public function importEmployees(Request $request)
    {
        // Handle the import functionality here
        return redirect()->route('admin.app-management')->with('success', 'Employees imported successfully.');
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@usep\.edu\.ph$/'],
        ]);

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
