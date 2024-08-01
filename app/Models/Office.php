<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $primaryKey = 'office_id';

    protected $fillable = [
        'campus_id',
        'office_name',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
}
