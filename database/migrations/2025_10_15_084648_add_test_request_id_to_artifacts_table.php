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
        Schema::table('artifacts', function (Blueprint $table) {
            $table->unsignedBigInteger('test_request_id')->nullable()->after('qoyod_customer_id');
            $table->foreign('test_request_id')->references('id')->on('test_requests')->onDelete('set null');
            $table->index('test_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropForeign(['test_request_id']);
            $table->dropIndex(['test_request_id']);
            $table->dropColumn('test_request_id');
        });
    }
};