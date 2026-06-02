<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precious_metals_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();

            // 1. Job Information
            $table->date('test_date')->nullable();
            $table->string('test_location')->nullable();
            $table->string('item_product_id')->nullable();
            $table->string('receiving_record')->nullable();

            // 2. Product Information
            $table->string('product')->nullable();
            $table->string('product_number')->nullable();
            $table->string('ref_number')->nullable();
            $table->decimal('gross_weight', 12, 4)->nullable();
            $table->decimal('net_weight', 12, 4)->nullable();
            $table->json('metal_purities')->nullable();

            // 3. XRF Analyzer (elements × readings)
            $table->json('xrf_data')->nullable();

            // 4. Result
            $table->json('result_flags')->nullable();

            // 5. Comments
            $table->text('comments')->nullable();

            // 6. Grader
            $table->string('grader_name')->nullable();
            $table->date('grader_date')->nullable();
            $table->string('grader_signature')->nullable();

            // 12. Analytical equipment / technical interpretation
            $table->text('analytical_interpretation')->nullable();
            $table->string('analytical_name')->nullable();
            $table->date('analytical_date')->nullable();
            $table->string('analytical_signature')->nullable();

            // 8. Product Photography
            $table->string('image1_ref')->nullable();
            $table->string('image2_ref')->nullable();
            $table->string('image_taken_by')->nullable();
            $table->date('image_date')->nullable();
            $table->string('image_signature')->nullable();

            // 9. Retaining Information
            $table->string('retaining_place')->nullable();
            $table->string('retaining_by')->nullable();
            $table->date('retaining_date')->nullable();
            $table->string('retaining_signature')->nullable();

            // 10. Reporting Information
            $table->string('report_done')->nullable();
            $table->string('report_done_notes')->nullable();
            $table->string('label_done')->nullable();
            $table->string('label_done_notes')->nullable();
            $table->string('report_done_by')->nullable();
            $table->date('report_date')->nullable();
            $table->string('report_signature')->nullable();

            // 11. Report references / checked
            $table->string('report_number')->nullable();
            $table->string('report_number_pmr')->nullable();
            $table->string('checked_by')->nullable();
            $table->date('checked_date')->nullable();
            $table->string('checked_signature')->nullable();

            $table->string('status')->nullable();
            $table->boolean('is_final')->default(false);
            $table->timestamps();

            $table->index(['artifact_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precious_metals_evaluations');
    }
};
