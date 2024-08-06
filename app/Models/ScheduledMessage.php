<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    protected $fillable = ['message', 'recipients', 'status', 'scheduled_time'];

    protected $casts = [
        'recipients' => 'array', // To handle JSON conversion automatically
    ];
}
