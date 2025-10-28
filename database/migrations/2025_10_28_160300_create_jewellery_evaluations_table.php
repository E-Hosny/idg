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
        Schema::create('jewellery_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            
            // Job Information
            $table->date('test_date')->nullable();
            $table->string('test_location')->nullable();
            $table->string('item_product_id')->nullable();
            $table->string('receiving_record')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('approved_by')->nullable();
            
            // Product Information
            $table->string('product_number')->nullable();
            $table->string('style_number')->nullable();
            $table->string('ref_number')->nullable();
            $table->decimal('gross_weight', 10, 3)->nullable();
            $table->decimal('net_weight', 10, 3)->nullable();
            $table->json('product_types')->nullable();
            $table->json('metal_types')->nullable();
            $table->json('stamps')->nullable();
            
            // Lab-Grown Diamond Screen
            $table->enum('hpht_screen', ['Natural', 'Lab-Grown'])->nullable();
            $table->integer('hpht_diamond_pcs')->nullable();
            $table->enum('cvd_check', ['Natural', 'Lab-Grown'])->nullable();
            $table->integer('cvd_diamond_pcs')->nullable();
            $table->enum('need_unmount', ['Yes', 'No'])->nullable();
            $table->string('unmount_reason')->nullable();
            
            // XRF Data
            $table->json('xrf_data')->nullable();
            
            // Diamond/s - Side Stones
            $table->string('side_stones_weight_type')->nullable();
            $table->decimal('side_stones_weight', 10, 3)->nullable();
            $table->integer('side_stones_pieces')->nullable();
            $table->json('side_stones_shapes')->nullable();
            $table->json('side_stones_colours')->nullable();
            $table->json('side_stones_clarities')->nullable();
            
            // Diamond/s - Centre Stone
            $table->decimal('centre_stone_weight', 10, 3)->nullable();
            $table->string('centre_stone_shape')->nullable();
            $table->string('centre_stone_colour')->nullable();
            $table->string('centre_stone_clarity')->nullable();
            
            // Coloured Gemstones
            $table->decimal('coloured_stones_weight', 10, 3)->nullable();
            $table->string('coloured_stones_shape')->nullable();
            $table->integer('coloured_stones_count')->nullable();
            $table->string('coloured_stones_group')->nullable();
            $table->string('coloured_stones_species')->nullable();
            $table->enum('coloured_stones_conclusion', ['Natural', 'Synthetic'])->nullable();
            $table->text('coloured_stones_note')->nullable();
            
            // Result
            $table->string('result')->nullable();
            
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
            
            // Product Photography
            $table->string('image1_taken_by')->nullable();
            $table->date('image1_date')->nullable();
            $table->string('image1_signature')->nullable();
            $table->string('image2_taken_by')->nullable();
            $table->date('image2_date')->nullable();
            $table->string('image2_signature')->nullable();
            
            // Retaining Information
            $table->string('retaining_place')->nullable();
            $table->string('retaining_by')->nullable();
            $table->date('retaining_date')->nullable();
            $table->string('retaining_signature')->nullable();
            
            // Reporting Information
            $table->enum('report_done', ['Yes', 'No'])->nullable();
            $table->enum('label_done', ['Yes', 'No'])->nullable();
            $table->string('report_done_by')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('report_number')->nullable();
            
            // Metal Analysis
            $table->json('metal_analysis')->nullable();
            
            // Status
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
        Schema::dropIfExists('jewellery_evaluations');
    }
};
