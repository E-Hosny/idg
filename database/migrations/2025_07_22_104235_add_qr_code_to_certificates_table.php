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
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('qr_code_token')->unique()->nullable()->after('certificate_file_path');
            $table->string('qr_code_path')->nullable()->after('qr_code_token');
            $table->timestamp('qr_code_generated_at')->nullable()->after('qr_code_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['qr_code_token', 'qr_code_path', 'qr_code_generated_at']);
        });
    }
};
