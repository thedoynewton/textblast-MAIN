<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $primaryKey = 'major_id';

    protected $fillable = ['major_name', 'campus_id', 'college_id', 'program_id'];

    public function students()
    {
        return $this->hasMany(Student::class, 'major_id', 'major_id');
    }

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
}
