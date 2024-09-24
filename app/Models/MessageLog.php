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
        'total_recipients',
        'sent_count',
        'failed_count',
        'cancelled_at',
        'campus_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSuccessRateAttribute()
    {
        if ($this->total_recipients > 0) {
            return ($this->sent_count / $this->total_recipients) * 100;
        }
        return 0;
    }

    public function getIsFullyDeliveredAttribute()
    {
        return $this->total_recipients === $this->sent_count;
    }

    public function getIsCancelledAttribute()
    {
        return !is_null($this->cancelled_at);
    }

    public function messageRecipients()
    {
        return $this->hasMany(MessageRecipient::class, 'message_log_id');
    }
}
