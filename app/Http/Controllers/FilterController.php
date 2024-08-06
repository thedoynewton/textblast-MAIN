<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Program;
use App\Models\Year;
use App\Models\Office;
use App\Models\Status;
use App\Models\Type;

class FilterController extends Controller
{
    public function getFilters($type, $campusId, Request $request)
    {
        $recipientType = $request->query('recipient_type', 'both');

        $response = [];

        if ($type === 'students' || ($type === 'all' && $recipientType === 'students') || $recipientType === 'both') {
            $response['colleges'] = College::where('campus_id', $campusId)->get(['college_id as id', 'college_name as name']);
            $response['programs'] = Program::where('campus_id', $campusId)->get(['program_id as id', 'program_name as name']);
            $response['years'] = Year::all(['year_id as id', 'year_name as name']);
        }

        if ($type === 'employees' || ($type === 'all' && $recipientType === 'employees') || $recipientType === 'both') {
            $response['offices'] = Office::where('campus_id', $campusId)->get(['office_id as id', 'office_name as name']);
            $response['statuses'] = Status::all(['status_id as id', 'status_name as name']);
            $response['types'] = Type::where('campus_id', $campusId)->get(['type_id as id', 'type_name as name']);
        }

        return response()->json($response);
    }
}

