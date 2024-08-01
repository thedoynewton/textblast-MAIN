<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $primaryKey = 'year_id';

    protected $fillable = ['year_name'];

    public function students()
    {
        return $this->hasMany(Student::class, 'year_id', 'year_id');
    }
}
