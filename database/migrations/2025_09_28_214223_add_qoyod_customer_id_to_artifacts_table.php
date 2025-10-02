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
            $table->unsignedBigInteger('qoyod_customer_id')->nullable()->after('client_id');
            $table->index('qoyod_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropIndex(['qoyod_customer_id']);
            $table->dropColumn('qoyod_customer_id');
        });
    }
};
