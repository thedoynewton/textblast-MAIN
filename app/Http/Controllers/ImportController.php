<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import the Log facade
use App\Models\College;
use App\Models\Program;

class ImportController extends Controller
{
    public function importData(Request $request)
    {
        try {
            // Import Colleges
            $remoteColleges = DB::connection('sqlsrv1')->table('vw_college_TB')->select('CollegeID', 'CollegeName')->get();

            foreach ($remoteColleges as $remoteCollege) {
                College::updateOrCreate(
                    ['college_id' => $remoteCollege->CollegeID],
                    [
                        'college_name' => $remoteCollege->CollegeName,
                        'campus_id' => 1, // Assuming a default value or logic to determine campus_id
                        'updated_at' => now(),
                    ]
                );
            }

            // Import Programs
            $remotePrograms = DB::connection('sqlsrv1')->table('vw_es_programs_TB')->select('ProgID', 'ProgName', 'CollegeID')->get();

            foreach ($remotePrograms as $remoteProgram) {
                // Find the associated college
                $college = College::where('college_id', $remoteProgram->CollegeID)->first();

                if ($college) {
                    Program::updateOrCreate(
                        ['program_id' => $remoteProgram->ProgID],
                        [
                            'program_name' => $remoteProgram->ProgName,
                            'college_id' => $remoteProgram->CollegeID,
                            'campus_id' => $college->campus_id, // Use the college's campus_id
                            'updated_at' => now(),
                        ]
                    );
                } else {
                    // Log or handle the case where the college does not exist
                    Log::warning("College ID {$remoteProgram->CollegeID} not found. Program ID {$remoteProgram->ProgID} skipped.");
                }
            }

            return response()->json(['success' => 'Data imported successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
