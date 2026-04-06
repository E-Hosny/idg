<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_requests', function (Blueprint $table) {
            $table->string('redelivery_from_lab_signed_document_path')->nullable()->after('lab_delivery_signed_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('test_requests', function (Blueprint $table) {
            $table->dropColumn('redelivery_from_lab_signed_document_path');
        });
    }
};
