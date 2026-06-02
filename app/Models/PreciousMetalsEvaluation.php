<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreciousMetalsEvaluation extends Model
{
    protected $fillable = [
        'artifact_id',
        'evaluator_id',
        'test_date',
        'test_location',
        'item_product_id',
        'receiving_record',
        'product',
        'product_number',
        'ref_number',
        'gross_weight',
        'net_weight',
        'metal_purities',
        'xrf_data',
        'result_flags',
        'comments',
        'grader_name',
        'grader_date',
        'grader_signature',
        'analytical_interpretation',
        'analytical_name',
        'analytical_date',
        'analytical_signature',
        'image1_ref',
        'image2_ref',
        'image_taken_by',
        'image_date',
        'image_signature',
        'retaining_place',
        'retaining_by',
        'retaining_date',
        'retaining_signature',
        'report_done',
        'report_done_notes',
        'label_done',
        'label_done_notes',
        'report_done_by',
        'report_date',
        'report_signature',
        'report_number',
        'report_number_pmr',
        'checked_by',
        'checked_date',
        'checked_signature',
        'status',
        'is_final',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
            'grader_date' => 'date',
            'analytical_date' => 'date',
            'image_date' => 'date',
            'retaining_date' => 'date',
            'report_date' => 'date',
            'checked_date' => 'date',
            'gross_weight' => 'decimal:4',
            'net_weight' => 'decimal:4',
            'metal_purities' => 'array',
            'xrf_data' => 'array',
            'result_flags' => 'array',
            'is_final' => 'boolean',
        ];
    }

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
