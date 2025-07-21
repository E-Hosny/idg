<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiamondEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'artifact_id',
        'evaluator_id',
        
        // Job Information
        'test_date',
        'test_location',
        'item_product_id',
        'receiving_record',
        'prepared_by',
        'approved_by',
        
        // Stone Information
        'weight',
        'shape',
        'laser_inscription',
        
        // Lab-Grown Diamond Screen
        'hpht_screen',
        'cvd_check',
        
        // Proportion Grade
        'diameter',
        'total_depth',
        'table_percentage',
        'star_facet',
        'crown_angle',
        'crown_height',
        'girdle_thickness_min',
        'girdle_thickness_max',
        'pavilion_depth',
        'pavilion_angle',
        'lower_girdle',
        'culet_size',
        'girdle_condition',
        'culet_condition',
        
        // Grade Information
        'polish',
        'symmetry',
        'cut',
        
        // Visual Inspection
        'clarity',
        'colour',
        
        // Fluorescence
        'fluorescence_strength',
        'fluorescence_colour',
        
        // Result
        'result',
        'stone_type',
        
        // Comments
        'comments',
        
        // Grader
        'grader_name',
        'grader_date',
        'grader_signature',
        
        // Analytical Equipment
        'analytical_name',
        'analytical_date',
        'analytical_signature',
        
        // Retaining Information
        'retaining_place',
        'retaining_by',
        'retaining_date',
        'retaining_signature',
        
        // Reporting Information
        'report_done',
        'label_done',
        'report_done_by',
        'report_date',
        'checked_by',
        'report_number',
        
        // Status
        'status',
        'is_final',
    ];

    protected $casts = [
        'test_date' => 'date',
        'grader_date' => 'date',
        'analytical_date' => 'date',
        'retaining_date' => 'date',
        'report_date' => 'date',
        'weight' => 'decimal:3',
        'diameter' => 'decimal:2',
        'total_depth' => 'decimal:2',
        'table_percentage' => 'decimal:2',
        'star_facet' => 'decimal:2',
        'crown_angle' => 'decimal:2',
        'crown_height' => 'decimal:2',
        'girdle_thickness_min' => 'decimal:2',
        'girdle_thickness_max' => 'decimal:2',
        'pavilion_depth' => 'decimal:2',
        'pavilion_angle' => 'decimal:2',
        'lower_girdle' => 'decimal:2',
        'is_final' => 'boolean',
    ];

    /**
     * Get the artifact that owns the evaluation.
     */
    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    /**
     * Get the evaluator (user) that owns the evaluation.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Scope a query to only include completed evaluations.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include approved evaluations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include final evaluations.
     */
    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }

    /**
     * Mark evaluation as completed.
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Mark evaluation as approved.
     */
    public function markAsApproved()
    {
        $this->update(['status' => 'approved', 'is_final' => true]);
    }

    /**
     * Get evaluation status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'completed' => 'blue',
            'approved' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get evaluation status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'completed' => 'Completed',
            'approved' => 'Approved',
            default => 'Unknown'
        };
    }
}
