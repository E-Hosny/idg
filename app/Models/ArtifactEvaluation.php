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
        // General evaluation fields
        'test_date',
        'test_location',
        'weight',
        'colour',
        'transparency',
        'lustre',
        'tone',
        'phenomena',
        'saturation',
        'measurements',
        'shape_cut',
        'pleochroism',
        'optic_character',
        'refractive_index',
        'ri_result',
        'inclusion',
        'weight_air',
        'weight_water',
        'sg_result',
        'fluorescence_long',
        'fluorescence_short',
        'result',
        'variety',
        'species_group',
        'comments',
        'grader_name',
        'grader_date',
        'analytical_interpretation',
        'retaining_place',
        'retained_by',
        'retained_date',
        'report_done',
        'label_done',
        'checked_by',
        'checked_date',
        // Jewellery evaluation fields
        'product_type',
        'metal_type',
        'stamp',
        'diamond_count',
        'diamond_weight',
        'diamond_shape',
        'diamond_colour',
        'diamond_clarity',
        'diamond_conclusion',
        'diamond_note',
        'coloured_stones_weight',
        'coloured_stones_shape',
        'coloured_stones_count',
        'coloured_stones_group',
        'coloured_stones_species',
        'coloured_stones_conclusion',
        'coloured_stones_note',
        'grader_signature',
        'analytical_name',
        'analytical_signature',
        'image1_taken_by',
        'image1_date',
        'image1_signature',
        'image2_taken_by',
        'image2_date',
        'image2_signature',
        'retaining_signature',
        'report_done_by',
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
        // General evaluation casts
        'test_date' => 'date',
        'weight' => 'decimal:3',
        'weight_air' => 'decimal:3',
        'weight_water' => 'decimal:3',
        'refractive_index' => 'array',
        'grader_date' => 'date',
        'retained_date' => 'date',
        'report_done' => 'boolean',
        'label_done' => 'boolean',
        'checked_date' => 'date',
        // Jewellery evaluation casts
        'diamond_weight' => 'decimal:3',
        'coloured_stones_weight' => 'decimal:3',
        'image1_date' => 'date',
        'image2_date' => 'date',
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
