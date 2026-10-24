<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_survey_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('qoyod_customer_id')->index();
            $table->foreignId('test_request_id')->nullable()->constrained('test_requests')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('recipient_email');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->unique()->constrained('customer_survey_invitations')->cascadeOnDelete();
            $table->string('language', 2);
            $table->string('customer_name');
            $table->string('mobile');
            $table->unsignedTinyInteger('service_quality_rating');
            $table->unsignedTinyInteger('delivery_time_rating');
            $table->unsignedTinyInteger('report_quality_rating');
            $table->string('signer_name');
            $table->boolean('has_complaint')->default(false);
            $table->text('complaint_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_survey_responses');
        Schema::dropIfExists('customer_survey_invitations');
    }
};
