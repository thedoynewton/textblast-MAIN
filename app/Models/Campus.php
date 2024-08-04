<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $table = 'campuses'; // Matches the table name in your database

    protected $primaryKey = 'campus_id';

    protected $fillable = ['campus_name'];

    public function students()
    {
        return $this->hasMany(Student::class, 'campus_id', 'campus_id');
    }

    public function colleges()
    {
        return $this->hasMany(College::class, 'campus_id', 'campus_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'campus_id', 'campus_id');
    }

    public function majors()
    {
        return $this->hasMany(Major::class, 'campus_id', 'campus_id');
    }
}
