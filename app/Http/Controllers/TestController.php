<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtifactEvaluation;
use App\Models\Artifact;

class TestController extends Controller
{
    /**
     * Test endpoint for debugging evaluation issues on server
     */
    public function testEvaluation(Request $request)
    {
        \Log::info('testEvaluation endpoint called');
        
        try {
            // Test 1: Check if we can create a basic evaluation
            $testData = [
                'artifact_id' => 1,
                'evaluator_id' => 1,
                'test_date' => '2025-08-06',
                'test_location' => 'test',
                'weight' => 12,
                'colour' => 'black',
                'transparency' => 'Translucent',
                'lustre' => 'Resinous',
                'tone' => 'Medium',
                'phenomena' => '21',
                'saturation' => 'Weak',
                'is_final' => true,
                'evaluation_date' => now(),
                'detailed_notes' => ['en' => '', 'ar' => ''],
                'supporting_documents' => []
            ];

            \Log::info('Test data:', $testData);

            $evaluation = ArtifactEvaluation::create($testData);
            \Log::info('Test evaluation created successfully:', $evaluation->toArray());

            // Test 2: Check database connection and table structure
            $columns = \DB::select('DESCRIBE artifact_evaluations');
            $columnNames = array_column($columns, 'Field');
            
            \Log::info('Database columns:', $columnNames);

            // Test 3: Check if artifact exists
            $artifact = Artifact::find(1);
            if ($artifact) {
                \Log::info('Artifact found:', $artifact->toArray());
            } else {
                \Log::warning('Artifact with ID 1 not found');
            }

            return response()->json([
                'success' => true,
                'test_evaluation_id' => $evaluation->id,
                'columns' => $columnNames,
                'artifact_exists' => $artifact ? true : false,
                'message' => 'All tests passed'
            ]);

        } catch (\Exception $e) {
            \Log::error('Test evaluation failed:', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ], 500);
        }
    }
} 