<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'stud_id';

    protected $fillable = [
        'stud_fname',
        'stud_lname',
        'stud_mname',
        'stud_contact',
        'stud_email',
        'campus_id',
        'college_id',
        'program_id',
        'major_id',
        'year_id',
        'enrollment_stat',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id', 'campus_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'college_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id', 'major_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id', 'year_id');
    }
}
