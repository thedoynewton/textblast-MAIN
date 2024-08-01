<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $primaryKey = 'program_id';

    protected $fillable = ['program_name', 'campus_id', 'college_id'];

    public function students()
    {
        return $this->hasMany(Student::class, 'program_id', 'program_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id', 'campus_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'college_id');
    }

    public function majors()
    {
        return $this->hasMany(Major::class, 'program_id', 'program_id');
    }
}
