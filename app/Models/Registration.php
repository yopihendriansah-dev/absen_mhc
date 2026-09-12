<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    public const STATUS_REGISTERED = 'registered';
    public const STATUS_CANCELLED = 'cancelled';
    public const INVITATION_PENDING = 'pending';
    public const INVITATION_SENT = 'sent';
    public const INVITATION_FAILED = 'failed';

    protected $fillable = [
        'event_id',
        'registration_code',
        'name',
        'email',
        'phone',
        'gender',
        'city',
        'organization',
        'notes',
        'referral_source',
        'data_consent_at',
        'status',
        'invitation_status',
        'invitation_sent_at',
        'invitation_send_count',
        'last_invitation_error',
    ];

    protected function casts(): array
    {
        return [
            'data_consent_at' => 'datetime',
            'invitation_sent_at' => 'datetime',
            'invitation_send_count' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }
}
