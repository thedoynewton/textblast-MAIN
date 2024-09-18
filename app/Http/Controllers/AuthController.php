<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        //redirect to google
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Login cancelled or failed. Please try again.');
        }

        $email = $user->getEmail();
        $domain = substr(strrchr($email, "@"), 1);

        if ($domain !== 'usep.edu.ph') {
            return redirect('/')->with('error', 'Access denied. You must use your USeP email.');
        }

        $existingUser = User::where('email', $email)->first();

        if (!$existingUser) {
            return redirect('/')->with('error', 'Access denied. You do not have the required permissions.');
        }

        $existingUser->update([
            'name' => $user->getName(),
            'avatar' => $user->getAvatar(),
            'google_id' => $user->getId(),
            'updated_at' => now(),
            'remember_token' => $user->token,
        ]);

        Auth::login($existingUser, true);

        if ($existingUser->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($existingUser->role === 'subadmin') {
            return redirect()->route('subadmin.dashboard');
        } else {
            Auth::logout();
            return redirect('/')->with('error', 'Access denied. You do not have the required permissions.');
        }
    }

    public function loginWithEmail(Request $request)
    {
        // Log the login attempt
        Log::info('Login attempt with email: ' . $request->input('email'));
    
        // Validate the email format
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
    
        if ($validator->fails()) {
            // Log validation failure
            Log::error('Email format validation failed', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Extract the domain from the email
        $email = $request->input('email');
        $domain = substr(strrchr($email, "@"), 1);
    
        // Log the extracted domain
        Log::info('Email domain check: ' . $domain);
    
        // Check if the email domain is 'usep.edu.ph'
        if ($domain !== 'usep.edu.ph') {
            Log::warning('Access denied: Non-USeP email used for login', ['email' => $email]);
            // Flash error message to session and redirect back to the login form
            return redirect('/')->with('error', 'Access denied. You must use your USeP email.');
        }
    
        // Now validate the existence of the email in the users table
        $user = User::where('email', $email)->first();
    
        if ($user) {
            // Log user found
            Log::info('User found with email: ' . $user->email . ' | Role: ' . ($user->role ?? 'No role assigned'));
    
            // Log the user in
            Auth::login($user);
    
            // Check for the user's role
            if ($user->role === 'admin') {
                Log::info('User logged in as admin.');
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'subadmin') {
                Log::info('User logged in as subadmin.');
                return redirect()->route('subadmin.dashboard');
            } else {
                // If the user does not have a role, log them out and show the access denied error
                Log::warning('User login failed due to missing role.', ['email' => $user->email]);
                Auth::logout();
                return redirect('/')->with('error', 'Access denied. You do not have the required permissions.');
            }
        } else {
            // Log user not found
            Log::error('User not found for email: ' . $email);
            return redirect()->back()->with('error', 'Login failed. Please try again.');
        }
    }
    
    

    public function logout()
    {
        //logout user
        Auth::logout();
        return redirect('/');
    }

    public function index()
    {
        if (Auth::check()) {
            // Redirect authenticated users to their appropriate dashboard
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'subadmin') {
                return redirect()->route('subadmin.dashboard');
            }
        }
    
        // If not authenticated, show the welcome page
        return view('welcome');
    }
    
}