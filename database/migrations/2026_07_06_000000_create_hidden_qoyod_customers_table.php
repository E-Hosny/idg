<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hidden_qoyod_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qoyod_customer_id')->unique();
            $table->foreignId('hidden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hidden_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hidden_qoyod_customers');
    }
};
