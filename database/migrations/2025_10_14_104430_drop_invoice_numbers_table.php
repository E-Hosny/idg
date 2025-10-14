<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('invoice_numbers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('invoice_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // e.g., 'invoice', 'quote', etc.
            $table->integer('current_number')->default(0); // Current sequence number
            $table->string('prefix')->default('INV'); // Prefix for the number
            $table->timestamps();
        });
        
        // Insert initial record for invoices
        DB::table('invoice_numbers')->insert([
            'type' => 'invoice',
            'current_number' => 0,
            'prefix' => 'INV',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
};
