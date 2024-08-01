<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $primaryKey = 'college_id';

    protected $fillable = ['college_name', 'campus_id'];

    public function students()
    {
        return $this->hasMany(Student::class, 'college_id', 'college_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id', 'campus_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'college_id', 'college_id');
    }

    public function majors()
    {
        return $this->hasMany(Major::class, 'college_id', 'college_id');
    }
}
