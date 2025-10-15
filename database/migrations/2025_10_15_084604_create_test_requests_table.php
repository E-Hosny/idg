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
        Schema::create('test_requests', function (Blueprint $table) {
            $table->id();
            $table->string('qoyod_customer_id'); // ربط مع عميل قيود
            $table->string('receiving_record_no')->unique(); // رقم سجل الاستلام
            $table->date('received_date'); // تاريخ الاستلام
            $table->string('received_in')->nullable(); // استلم في
            $table->date('delivery_date')->nullable(); // تاريخ التسليم المتوقع
            $table->string('received_by')->nullable(); // تم الاستلام بواسطة
            $table->enum('status', ['pending', 'under_evaluation', 'evaluated', 'certified', 'delivered'])->default('pending');
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
            
            // فهارس للبحث السريع
            $table->index('qoyod_customer_id');
            $table->index('receiving_record_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_requests');
    }
};