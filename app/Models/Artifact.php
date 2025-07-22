<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Artifact extends Model
{
    use HasTranslations;

    protected $fillable = [
        'client_id',
        'artifact_code',
        'type',
        'service',
        'weight',
        'weight_unit',
        'price',
        'notes',
        'delivery_type',
        'title',
        'description',
        'category_id',
        'origin_country',
        'origin_city',
        'period',
        'material',
        'dimensions',
        'condition',
        'estimated_value',
        'currency',
        'images',
        'acquisition_notes',
        'acquisition_date',
        'previous_owner',
        'provenance',
        'status',
        'is_authentic',
        'assigned_to',
        'evaluation_deadline',
        'internal_notes',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'dimensions' => 'array',
        'images' => 'array',
        'provenance' => 'array',
        'estimated_value' => 'decimal:2',
        'acquisition_date' => 'date',
        'evaluation_deadline' => 'datetime',
        'is_authentic' => 'boolean',
    ];

    public $translatable = ['title', 'description'];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(ArtifactEvaluation::class);
    }

    public function finalEvaluation()
    {
        return $this->hasOne(ArtifactEvaluation::class)->where('is_final', true);
    }

    public function diamondEvaluations(): HasMany
    {
        return $this->hasMany(DiamondEvaluation::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function latestCertificate()
    {
        return $this->hasOne(Certificate::class)->latest();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getTitleAttribute($value)
    {
        $titles = json_decode($value, true);
        return $titles[app()->getLocale()] ?? $titles['en'] ?? '';
    }

    public function getDescriptionAttribute($value)
    {
        $descriptions = json_decode($value, true);
        return $descriptions[app()->getLocale()] ?? $descriptions['en'] ?? '';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => __('Pending'),
            'under_evaluation' => __('Under Evaluation'),
            'evaluated' => __('Evaluated'),
            'certified' => __('Certified'),
            'rejected' => __('Rejected'),
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getConditionLabelAttribute()
    {
        $labels = [
            'excellent' => __('Excellent'),
            'very_good' => __('Very Good'),
            'good' => __('Good'),
            'fair' => __('Fair'),
            'poor' => __('Poor'),
        ];

        return $labels[$this->condition] ?? $this->condition;
    }

    // Helper methods
    public static function generateArtifactCode()
    {
        $year = date('Y');
        $lastArtifact = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $number = $lastArtifact ? (int) substr($lastArtifact->artifact_code, -3) + 1 : 1;
        
        return 'IDG-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
