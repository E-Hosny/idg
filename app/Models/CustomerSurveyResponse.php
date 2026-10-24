<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSurveyResponse extends Model
{
    protected $fillable = [
        'invitation_id',
        'language',
        'customer_name',
        'mobile',
        'service_quality_rating',
        'delivery_time_rating',
        'report_quality_rating',
        'signer_name',
        'has_complaint',
        'complaint_details',
    ];

    protected $casts = [
        'has_complaint' => 'boolean',
        'service_quality_rating' => 'integer',
        'delivery_time_rating' => 'integer',
        'report_quality_rating' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(CustomerSurveyInvitation::class, 'invitation_id');
    }
}
