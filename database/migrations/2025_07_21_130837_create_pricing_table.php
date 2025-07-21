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
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->string('artifact_type'); // نوع القطعة
            $table->string('service_type'); // نوع الخدمة
            $table->decimal('min_weight', 8, 2); // الحد الأدنى للوزن
            $table->decimal('max_weight', 8, 2)->nullable(); // الحد الأقصى للوزن (null يعني لا حد أقصى)
            $table->decimal('price', 10, 2); // السعر
            $table->timestamps();
            
            // فهارس للبحث السريع
            $table->index(['artifact_type', 'service_type', 'min_weight']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
