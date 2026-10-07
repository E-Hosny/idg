<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qoyod_customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qoyod_customer_id')->unique();
            $table->string('company_representative_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qoyod_customer_profiles');
    }
};
