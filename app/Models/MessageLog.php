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
        'status',
        'total_recipients',       // Field to track the total number of recipients
        'sent_count',             // Field to track the number of successfully sent messages
        'failed_count',           // Field to track the number of failed messages
        'cancelled_at',           // Field to track the timestamp when a message was cancelled
        'campus_id',  // Add campus_id to fillable attributes
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
    
    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'cancelled_at' => 'datetime', // Cast cancelled_at to datetime
    ];

    /**
     * Get the user that sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the success rate of the message delivery.
     *
     * @return float
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_recipients > 0) {
            return ($this->sent_count / $this->total_recipients) * 100;
        }

        return 0;
    }

    /**
     * Determine if the message was fully delivered.
     *
     * @return bool
     */
    public function getIsFullyDeliveredAttribute()
    {
        return $this->total_recipients === $this->sent_count;
    }
    /**
     * Check if the message was cancelled.
     *
     * @return bool
     */
    public function getIsCancelledAttribute()
    {
        return !is_null($this->cancelled_at);
    }
}
