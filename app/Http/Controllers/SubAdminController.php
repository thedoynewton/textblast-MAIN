<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Campus;
use App\Models\Office;
use App\Models\Status;
use App\Models\College;
use App\Models\Program;
use App\Models\Type;

use Illuminate\Http\Request;

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

        return view('subadmin.messages', compact('campuses', 'colleges', 'programs', 'years', 'offices', 'statuses', 'types'));
    }

    public function broadcastMessages(Request $request)
    {
        return app(MessageController::class)->broadcastToRecipients($request);
    }

    public function analytics()
    {
        return view('subadmin.analytics');
    }
}
