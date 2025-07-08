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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('national_id')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('company_name')->nullable();
            $table->string('customer_code')->nullable();
            $table->date('received_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
