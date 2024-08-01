<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubAdminController extends Controller
{
    public function dashboard()
    {
        return view('subadmin.dashboard');
    }

    public function messages()
    {
        return view('subadmin.messages');
    }

    public function analytics()
    {
        return view('subadmin.analytics');
    }
}
