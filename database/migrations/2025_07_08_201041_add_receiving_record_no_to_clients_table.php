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
        if (!Schema::hasColumn('clients', 'receiving_record_no')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('receiving_record_no')->unique()->nullable();
            });
        }
        // معالجة القيم القديمة
        \DB::table('clients')->whereNull('receiving_record_no')->orWhere('receiving_record_no', '')->update([
            'receiving_record_no' => \DB::raw("CONCAT('TEMP-', id)")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('clients', 'receiving_record_no')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropUnique(['receiving_record_no']);
                $table->dropColumn('receiving_record_no');
            });
        }
    }
};
