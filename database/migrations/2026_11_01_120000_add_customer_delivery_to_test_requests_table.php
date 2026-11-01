<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_requests', function (Blueprint $table) {
            $table->timestamp('customer_delivered_at')->nullable()->after('lab_delivery_signed_document_path');
            $table->string('customer_delivered_by')->nullable()->after('customer_delivered_at');
            $table->string('customer_received_by')->nullable()->after('customer_delivered_by');
            $table->text('customer_delivery_note')->nullable()->after('customer_received_by');
        });
    }

    public function down(): void
    {
        Schema::table('test_requests', function (Blueprint $table) {
            $table->dropColumn([
                'customer_delivered_at',
                'customer_delivered_by',
                'customer_received_by',
                'customer_delivery_note',
            ]);
        });
    }
};
