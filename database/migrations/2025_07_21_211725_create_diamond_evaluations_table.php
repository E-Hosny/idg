<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diamond_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            
            // Job Information
            $table->date('test_date');
            $table->string('test_location')->nullable();
            $table->string('item_product_id')->nullable();
            $table->string('receiving_record')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('approved_by')->nullable();
            
            // Stone Information
            $table->decimal('weight', 8, 3)->nullable();
            $table->string('shape')->nullable();
            $table->enum('laser_inscription', ['Yes', 'No'])->default('No');
            
            // Lab-Grown Diamond Screen
            $table->enum('hpht_screen', ['Natural', 'Lab-Grown'])->nullable();
            $table->enum('cvd_check', ['Natural', 'Lab-Grown'])->nullable();
            
            // Proportion Grade
            $table->decimal('diameter', 5, 2)->nullable();
            $table->decimal('total_depth', 5, 2)->nullable();
            $table->decimal('table_percentage', 5, 2)->nullable();
            $table->decimal('star_facet', 5, 2)->nullable();
            $table->decimal('crown_angle', 5, 2)->nullable();
            $table->decimal('crown_height', 5, 2)->nullable();
            $table->decimal('girdle_thickness_min', 5, 2)->nullable();
            $table->decimal('girdle_thickness_max', 5, 2)->nullable();
            $table->decimal('pavilion_depth', 5, 2)->nullable();
            $table->decimal('pavilion_angle', 5, 2)->nullable();
            $table->decimal('lower_girdle', 5, 2)->nullable();
            $table->string('culet_size')->nullable();
            $table->string('girdle_condition')->nullable();
            $table->string('culet_condition')->nullable();
            
            // Grade Information
            $table->enum('polish', ['Excellent', 'Very Good', 'Good', 'Fair', 'Poor'])->nullable();
            $table->enum('symmetry', ['Excellent', 'Very Good', 'Good', 'Fair', 'Poor'])->nullable();
            $table->enum('cut', ['Excellent', 'Very Good', 'Good', 'Fair', 'Poor'])->nullable();
            
            // Visual Inspection
            $table->enum('clarity', ['FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2', 'I1', 'I2', 'I3'])->nullable();
            $table->enum('colour', ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'])->nullable();
            
            // Fluorescence
            $table->enum('fluorescence_strength', ['V. Str.', 'Str.', 'Med.', 'Faint', 'None'])->nullable();
            $table->string('fluorescence_colour')->nullable();
            
            // Result
            $table->enum('result', ['Pass', 'Fail', 'Reject'])->nullable();
            $table->enum('stone_type', ['Natural', 'Synthetic', 'Imitation', 'Treated'])->nullable();
            
            // Comments
            $table->text('comments')->nullable();
            
            // Grader
            $table->string('grader_name')->nullable();
            $table->date('grader_date')->nullable();
            $table->string('grader_signature')->nullable();
            
            // Analytical Equipment
            $table->string('analytical_name')->nullable();
            $table->date('analytical_date')->nullable();
            $table->string('analytical_signature')->nullable();
            
            // Retaining Information
            $table->string('retaining_place')->nullable();
            $table->string('retaining_by')->nullable();
            $table->date('retaining_date')->nullable();
            $table->string('retaining_signature')->nullable();
            
            // Reporting Information
            $table->enum('report_done', ['Yes', 'No'])->nullable();
            $table->enum('label_done', ['Yes', 'No'])->nullable();
            $table->string('report_done_by')->nullable();
            $table->date('report_date')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('report_number')->nullable();
            
            // Status and metadata
            $table->enum('status', ['draft', 'completed', 'approved'])->default('draft');
            $table->boolean('is_final')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['artifact_id', 'status']);
            $table->index(['evaluator_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diamond_evaluations');
    }
};
