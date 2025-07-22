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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->onDelete('cascade');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            
            // Certificate Information
            $table->string('certificate_number')->unique();
            $table->date('received_date');
            $table->date('report_date');
            $table->date('test_date');
            
            // Gemstone Information
            $table->string('identification');
            $table->string('shape_cut')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('color')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('transparency')->nullable();
            $table->text('phenomena')->nullable();
            $table->string('species')->nullable();
            $table->string('variety')->nullable();
            $table->string('group')->nullable();
            $table->string('origin_opinion')->nullable();
            $table->string('conclusion');
            $table->text('comments')->nullable();
            
            // Certificate Status
            $table->enum('status', ['draft', 'issued', 'cancelled'])->default('draft');
            $table->string('test_method')->nullable();
            $table->string('report_number')->nullable();
            
            // File path for generated certificate
            $table->string('certificate_file_path')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['artifact_id', 'status']);
            $table->index(['certificate_number']);
            $table->index(['generated_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
