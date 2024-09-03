<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Program;
use App\Models\Status;
use App\Models\Student;
use App\Models\Type;
use App\Models\Year;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getFilters($type, $campusId, Request $request)
    {
        $recipientType = $request->query('recipient_type', 'both');

        $response = [];

        if ($campusId === 'all') {
            // Fetch data for all campuses
            if ($type === 'students' || ($type === 'all' && $recipientType === 'students') || $recipientType === 'both') {
                $response['colleges'] = College::all(['college_id as id', 'college_name as name']);
                $response['years'] = Year::all(['year_id as id', 'year_name as name']);
            }

            if ($type === 'employees' || ($type === 'all' && $recipientType === 'employees') || $recipientType === 'both') {
                $response['offices'] = Office::all(['office_id as id', 'office_name as name']);
                $response['statuses'] = Status::all(['status_id as id', 'status_name as name']);
            }
        } else {
            // Fetch data for a specific campus
            if ($type === 'students' || ($type === 'all' && $recipientType === 'students') || $recipientType === 'both') {
                $response['colleges'] = College::where('campus_id', $campusId)->get(['college_id as id', 'college_name as name']);
                $response['years'] = Year::all(['year_id as id', 'year_name as name']);
            }

            if ($type === 'employees' || ($type === 'all' && $recipientType === 'employees') || $recipientType === 'both') {
                $response['offices'] = Office::where('campus_id', $campusId)->get(['office_id as id', 'office_name as name']);
                $response['statuses'] = Status::all(['status_id as id', 'status_name as name']);
            }
        }

        return response()->json($response);
    }

    public function getProgramsByCollege($collegeId)
    {
        $programs = ($collegeId === 'all') 
            ? Program::all(['program_id as id', 'program_name as name'])
            : Program::where('college_id', $collegeId)->get(['program_id as id', 'program_name as name']);

        return response()->json(['programs' => $programs]);
    }

    public function getTypesByOffice($campusId, $officeId, $statusId = null)
    {
        $query = Type::query();

        if ($campusId !== 'all') {
            $query->where('campus_id', $campusId);
        }

        if ($officeId !== 'all') {
            $query->where('office_id', $officeId);
        }

        if ($statusId && $statusId !== 'all') {
            $query->where('status_id', $statusId);
        }

        $types = $query->get(['type_id as id', 'type_name as name']);

        return response()->json(['types' => $types]);
    }

    public function getContacts(Request $request)
{
    $campusId = $request->query('campus');
    $filter = $request->query('filter');

    $studentsQuery = Student::query();
    $employeesQuery = Employee::query();

    if ($campusId && $campusId !== 'all') {
        $studentsQuery->where('campus_id', $campusId);
        $employeesQuery->where('campus_id', $campusId);
    }

    if ($filter === 'students') {
        $results = $studentsQuery->get();
    } elseif ($filter === 'employees') {
        $results = $employeesQuery->get();
    } else {
        $results = $studentsQuery->get()->concat($employeesQuery->get());
    }

    $formattedResults = $results->map(function ($item) {
        return [
            'stud_fname' => $item->stud_fname ?? $item->emp_fname,
            'stud_lname' => $item->stud_lname ?? $item->emp_lname,
            'stud_mname' => $item->stud_mname ?? $item->emp_mname,
            'stud_contact' => $item->stud_contact ?? $item->emp_contact,
            'stud_email' => $item->stud_email ?? $item->emp_email,
        ];
    });

    return response()->json($formattedResults);
}

}
