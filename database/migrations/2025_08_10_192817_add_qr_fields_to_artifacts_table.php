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
            $table->string('qr_token')->unique()->nullable()->comment('Unique token for QR code verification');
            $table->string('qr_code_path')->nullable()->comment('Path to generated QR code file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_code_path']);
        });
    }
};
