<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JewelleryEvaluation extends Model
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
        
        // Product Information
        'product_number',
        'style_number',
        'ref_number',
        'gross_weight',
        'net_weight',
        'product_types',
        'metal_types',
        'stamps',
        
        // Lab-Grown Diamond Screen
        'hpht_screen',
        'hpht_diamond_pcs',
        'cvd_check',
        'cvd_diamond_pcs',
        'need_unmount',
        'unmount_reason',
        
        // XRF Data
        'xrf_data',
        
        // Diamond/s - Side Stones
        'side_stones_weight_type',
        'side_stones_weight',
        'side_stones_pieces',
        'side_stones_shapes',
        'side_stones_colours',
        'side_stones_clarities',
        
        // Diamond/s - Centre Stone
        'centre_stone_weight',
        'centre_stone_shape',
        'centre_stone_colour',
        'centre_stone_clarity',
        
        // Coloured Gemstones
        'coloured_stones_weight',
        'coloured_stones_shape',
        'coloured_stones_count',
        'coloured_stones_group',
        'coloured_stones_species',
        'coloured_stones_conclusion',
        'coloured_stones_note',
        
        // Result
        'result',
        
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
        
        // Product Photography
        'image1_taken_by',
        'image1_date',
        'image1_signature',
        'image2_taken_by',
        'image2_date',
        'image2_signature',
        
        // Retaining Information
        'retaining_place',
        'retaining_by',
        'retaining_date',
        'retaining_signature',
        
        // Reporting Information
        'report_done',
        'label_done',
        'report_done_by',
        'checked_by',
        'report_number',
        
        // Metal Analysis
        'metal_analysis',
        
        // Status
        'status',
        'is_final',
    ];

    protected $casts = [
        'test_date' => 'date',
        'test_location' => 'string',
        'item_product_id' => 'string',
        'receiving_record' => 'string',
        'prepared_by' => 'string',
        'approved_by' => 'string',
        
        'product_number' => 'string',
        'style_number' => 'string',
        'ref_number' => 'string',
        'gross_weight' => 'decimal:3',
        'net_weight' => 'decimal:3',
        'product_types' => 'array',
        'metal_types' => 'array',
        'stamps' => 'array',
        
        'hpht_screen' => 'string',
        'hpht_diamond_pcs' => 'integer',
        'cvd_check' => 'string',
        'cvd_diamond_pcs' => 'integer',
        'need_unmount' => 'string',
        'unmount_reason' => 'string',
        
        'xrf_data' => 'array',
        
        'side_stones_weight_type' => 'string',
        'side_stones_weight' => 'decimal:3',
        'side_stones_pieces' => 'integer',
        'side_stones_shapes' => 'array',
        'side_stones_colours' => 'array',
        'side_stones_clarities' => 'array',
        
        'centre_stone_weight' => 'decimal:3',
        'centre_stone_shape' => 'string',
        'centre_stone_colour' => 'string',
        'centre_stone_clarity' => 'string',
        
        'coloured_stones_weight' => 'decimal:3',
        'coloured_stones_shape' => 'string',
        'coloured_stones_count' => 'integer',
        'coloured_stones_group' => 'string',
        'coloured_stones_species' => 'string',
        'coloured_stones_conclusion' => 'string',
        'coloured_stones_note' => 'string',
        
        'result' => 'string',
        'comments' => 'string',
        
        'grader_name' => 'string',
        'grader_date' => 'date',
        'grader_signature' => 'string',
        
        'analytical_name' => 'string',
        'analytical_date' => 'date',
        'analytical_signature' => 'string',
        
        'image1_taken_by' => 'string',
        'image1_date' => 'date',
        'image1_signature' => 'string',
        'image2_taken_by' => 'string',
        'image2_date' => 'date',
        'image2_signature' => 'string',
        
        'retaining_place' => 'string',
        'retaining_by' => 'string',
        'retaining_date' => 'date',
        'retaining_signature' => 'string',
        
        'report_done' => 'string',
        'label_done' => 'string',
        'report_done_by' => 'string',
        'checked_by' => 'string',
        'report_number' => 'string',
        
        'metal_analysis' => 'array',
        
        'status' => 'string',
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
}
