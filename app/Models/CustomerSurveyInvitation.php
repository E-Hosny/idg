<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerSurveyInvitation extends Model
{
    protected $fillable = [
        'token',
        'qoyod_customer_id',
        'test_request_id',
        'customer_name',
        'customer_phone',
        'recipient_email',
        'sent_by',
        'sent_at',
        'completed_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function testRequest(): BelongsTo
    {
        return $this->belongsTo(TestRequest::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function response(): HasOne
    {
        return $this->hasOne(CustomerSurveyResponse::class, 'invitation_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
