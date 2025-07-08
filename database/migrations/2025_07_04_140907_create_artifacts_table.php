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
        Schema::create('artifacts', function (Blueprint $table) {
            $table->id();
            $table->string('artifact_code')->unique(); // IDG-2024-001
            $table->json('title'); // {"en": "Ancient Roman Coin", "ar": "عملة رومانية قديمة"}
            $table->json('description'); // {"en": "...", "ar": "..."}
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('origin_country')->nullable();
            $table->string('origin_city')->nullable();
            $table->string('period')->nullable(); // "1st Century BC"
            $table->string('material')->nullable(); // "Gold, Silver, Bronze"
            $table->json('dimensions')->nullable(); // {"width": "2.5cm", "height": "2.5cm", "weight": "8.5g"}
            $table->enum('condition', ['excellent', 'very_good', 'good', 'fair', 'poor'])->default('good');
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('images')->nullable(); // ["image1.jpg", "image2.jpg"]
            $table->text('acquisition_notes')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('previous_owner')->nullable();
            $table->json('provenance')->nullable(); // detailed history
            $table->enum('status', ['pending', 'under_evaluation', 'evaluated', 'certified', 'rejected'])->default('pending');
            $table->boolean('is_authentic')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('evaluation_deadline')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'category_id']);
            $table->index(['assigned_to', 'status']);
            $table->index('artifact_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artifacts');
    }
};
