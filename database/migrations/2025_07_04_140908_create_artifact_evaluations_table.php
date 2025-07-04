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
        Schema::create('artifact_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->enum('evaluation_type', ['preliminary', 'detailed', 'final', 'second_opinion'])->default('preliminary');
            
            // Evaluation Scores (1-10)
            $table->integer('authenticity_score')->nullable(); // 1-10
            $table->integer('condition_score')->nullable(); // 1-10
            $table->integer('rarity_score')->nullable(); // 1-10
            $table->integer('historical_significance_score')->nullable(); // 1-10
            $table->integer('artistic_value_score')->nullable(); // 1-10
            $table->decimal('overall_score', 3, 1)->nullable(); // calculated average
            
            // Detailed Assessment
            $table->json('detailed_notes'); // {"en": "...", "ar": "..."}
            $table->text('authenticity_notes')->nullable();
            $table->text('condition_notes')->nullable();
            $table->text('historical_notes')->nullable();
            $table->text('recommendations')->nullable();
            
            // Valuation
            $table->decimal('estimated_min_value', 15, 2)->nullable();
            $table->decimal('estimated_max_value', 15, 2)->nullable();
            $table->string('valuation_currency', 3)->default('USD');
            $table->text('valuation_notes')->nullable();
            
            // Final Decision
            $table->enum('recommendation', ['accept', 'reject', 'needs_further_study', 'conditional_accept'])->nullable();
            $table->boolean('is_final')->default(false);
            $table->timestamp('evaluation_date');
            $table->json('supporting_documents')->nullable(); // ["doc1.pdf", "doc2.pdf"]
            
            $table->timestamps();

            $table->index(['artifact_id', 'evaluation_type']);
            $table->index(['evaluator_id', 'evaluation_date']);
            $table->index('is_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artifact_evaluations');
    }
};
