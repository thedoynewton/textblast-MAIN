<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    // Validate the email input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Find the user by email
    $user = User::where('email', $request->input('email'))->first();

    if ($user) {
        Auth::login($user);

        // Redirect to the appropriate dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'subadmin') {
            return redirect()->route('subadmin.dashboard');
        }
    }

    return redirect()->back()->with('error', 'Login failed. Please try again.');
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
