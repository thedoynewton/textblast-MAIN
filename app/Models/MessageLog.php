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
        'total_recipients',       // Add total_recipients field
        'sent_count',             // Add sent_count field
        'failed_count',           // Add failed_count field
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
}
