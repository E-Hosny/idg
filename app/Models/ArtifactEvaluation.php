<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ArtifactEvaluation extends Model
{
    use HasTranslations;

    protected $fillable = [
        'artifact_id',
        'evaluator_id',
        'evaluation_type',
        'authenticity_score',
        'condition_score',
        'rarity_score',
        'historical_significance_score',
        'artistic_value_score',
        'overall_score',
        'detailed_notes',
        'authenticity_notes',
        'condition_notes',
        'historical_notes',
        'recommendations',
        'estimated_min_value',
        'estimated_max_value',
        'valuation_currency',
        'valuation_notes',
        'recommendation',
        'is_final',
        'evaluation_date',
        'supporting_documents',
    ];

    protected $casts = [
        'detailed_notes' => 'array',
        'supporting_documents' => 'array',
        'authenticity_score' => 'integer',
        'condition_score' => 'integer',
        'rarity_score' => 'integer',
        'historical_significance_score' => 'integer',
        'artistic_value_score' => 'integer',
        'overall_score' => 'decimal:1',
        'estimated_min_value' => 'decimal:2',
        'estimated_max_value' => 'decimal:2',
        'is_final' => 'boolean',
        'evaluation_date' => 'datetime',
    ];

    public $translatable = ['detailed_notes'];

    // Relationships
    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // Scopes
    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }

    public function scopeByEvaluator($query, $evaluatorId)
    {
        return $query->where('evaluator_id', $evaluatorId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('evaluation_type', $type);
    }

    // Accessors
    public function getDetailedNotesAttribute($value)
    {
        $notes = json_decode($value, true);
        return $notes[app()->getLocale()] ?? $notes['en'] ?? '';
    }

    public function getEvaluationTypeLabelAttribute()
    {
        $labels = [
            'preliminary' => __('Preliminary'),
            'detailed' => __('Detailed'),
            'final' => __('Final'),
            'second_opinion' => __('Second Opinion'),
        ];

        return $labels[$this->evaluation_type] ?? $this->evaluation_type;
    }

    public function getRecommendationLabelAttribute()
    {
        $labels = [
            'accept' => __('Accept'),
            'reject' => __('Reject'),
            'needs_further_study' => __('Needs Further Study'),
            'conditional_accept' => __('Conditional Accept'),
        ];

        return $labels[$this->recommendation] ?? $this->recommendation;
    }

    // Mutators
    public function setOverallScoreAttribute($value)
    {
        if (!$value) {
            $scores = [
                $this->authenticity_score,
                $this->condition_score,
                $this->rarity_score,
                $this->historical_significance_score,
                $this->artistic_value_score,
            ];

            $validScores = array_filter($scores, fn($score) => $score !== null);
            if (count($validScores) > 0) {
                $this->attributes['overall_score'] = round(array_sum($validScores) / count($validScores), 1);
            }
        } else {
            $this->attributes['overall_score'] = $value;
        }
    }
}
