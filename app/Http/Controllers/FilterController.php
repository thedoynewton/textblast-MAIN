<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Office;
use App\Models\Program;
use App\Models\Status;
use App\Models\Type;
use App\Models\Year;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getFilters($type, $campusId)
    {
        $response = [];

        if ($type === 'students') {
            $response['colleges'] = College::where('campus_id', $campusId)->get(['college_id as id', 'college_name as name']);
            $response['years'] = Year::all(['year_id as id', 'year_name as name']);
        } else if ($type === 'employees') {
            $response['offices'] = Office::where('campus_id', $campusId)->get(['office_id as id', 'office_name as name']);
        }

        return response()->json($response);
    }

    public function getProgramsByCollege($collegeId)
    {
        $programs = Program::where('college_id', $collegeId)->get(['program_id as id', 'program_name as name']);
        return response()->json(['programs' => $programs]);
    }

    public function getTypesByOffice($officeId)
    {
        $types = Type::where('office_id', $officeId)->get(['type_id as id', 'type_name as name']);
        return response()->json(['types' => $types]);
    }
}
