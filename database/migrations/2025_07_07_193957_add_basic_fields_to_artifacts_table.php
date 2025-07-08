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
            $table->string('type')->nullable()->after('artifact_code');
            $table->string('service')->nullable()->after('type');
            $table->string('weight')->nullable()->after('service');
            $table->text('notes')->nullable()->after('weight');
            if (!Schema::hasColumn('artifacts', 'delivery_type')) {
                $table->string('delivery_type')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropColumn(['type', 'service', 'weight', 'notes']);
            if (Schema::hasColumn('artifacts', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }
        });
    }
};
