<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\PreciousMetalsEvaluation;
use Illuminate\Http\Request;

class PreciousMetalsEvaluationService
{
    /**
     * @return array<string, mixed>
     */
    public function validate(Request $request): array
    {
        return $request->validate([
            'test_date' => ['nullable', 'date'],
            'test_location' => ['nullable', 'string', 'max:255'],
            'item_product_id' => ['nullable', 'string', 'max:255'],
            'receiving_record' => ['nullable', 'string', 'max:255'],
            'product' => ['nullable', 'string', 'max:255'],
            'product_number' => ['nullable', 'string', 'max:255'],
            'ref_number' => ['nullable', 'string', 'max:255'],
            'gross_weight' => ['nullable', 'numeric'],
            'net_weight' => ['nullable', 'numeric'],
            'metal_purities' => ['nullable', 'array'],
            'xrf_data' => ['nullable', 'array'],
            'result_flags' => ['nullable', 'array'],
            'comments' => ['nullable', 'string'],
            'grader_name' => ['nullable', 'string', 'max:255'],
            'grader_date' => ['nullable', 'date'],
            'grader_signature' => ['nullable', 'string', 'max:255'],
            'analytical_interpretation' => ['nullable', 'string'],
            'analytical_name' => ['nullable', 'string', 'max:255'],
            'analytical_date' => ['nullable', 'date'],
            'analytical_signature' => ['nullable', 'string', 'max:255'],
            'image1_ref' => ['nullable', 'string', 'max:255'],
            'image2_ref' => ['nullable', 'string', 'max:255'],
            'image_taken_by' => ['nullable', 'string', 'max:255'],
            'image_date' => ['nullable', 'date'],
            'image_signature' => ['nullable', 'string', 'max:255'],
            'retaining_place' => ['nullable', 'string', 'max:255'],
            'retaining_by' => ['nullable', 'string', 'max:255'],
            'retaining_date' => ['nullable', 'date'],
            'retaining_signature' => ['nullable', 'string', 'max:255'],
            'report_done' => ['nullable', 'string', 'max:50'],
            'report_done_notes' => ['nullable', 'string', 'max:500'],
            'label_done' => ['nullable', 'string', 'max:50'],
            'label_done_notes' => ['nullable', 'string', 'max:500'],
            'report_done_by' => ['nullable', 'string', 'max:255'],
            'report_date' => ['nullable', 'date'],
            'report_signature' => ['nullable', 'string', 'max:255'],
            'report_number' => ['nullable', 'string', 'max:255'],
            'report_number_pmr' => ['nullable', 'string', 'max:255'],
            'checked_by' => ['nullable', 'string', 'max:255'],
            'checked_date' => ['nullable', 'date'],
            'checked_signature' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request, Artifact $artifact): PreciousMetalsEvaluation
    {
        $data = $this->validate($request);
        $data['artifact_id'] = $artifact->id;
        $data['evaluator_id'] = auth()->id();
        $data['status'] = 'completed';
        $data['is_final'] = true;

        return PreciousMetalsEvaluation::create($data);
    }

    public function update(Request $request, PreciousMetalsEvaluation $evaluation): PreciousMetalsEvaluation
    {
        $data = $this->validate($request);
        $evaluation->update($data);

        return $evaluation->fresh();
    }
}
