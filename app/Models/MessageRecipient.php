<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_log_id',
        'recipient_type',
        'stud_id',
        'emp_id',
        'first_name',
        'last_name',
        'middle_name',
        'contact_number',
        'email',
        'campus_id',
        'college_id',
        'program_id',
        'major_id',
        'year_id',
        'enrollment_stat',
        'office_id',
        'status_id',
        'type_id',
        'sent_status',
        'failure_reason',
    ];

    public function messageLog()
    {
        return $this->belongsTo(MessageLog::class);
    }
}
