<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_type',
        'content',
        'schedule',
        'scheduled_at',
        'sent_at',
        'status', // Add status here
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
    

    /**
     * Get the user that sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
