<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jewellery_evaluations', function (Blueprint $table) {
            if (! Schema::hasColumn('jewellery_evaluations', 'coloured_stones_cut')) {
                $table->string('coloured_stones_cut', 500)->nullable()->after('coloured_stones_shape');
            }
            if (! Schema::hasColumn('jewellery_evaluations', 'coloured_stones_variety')) {
                $table->string('coloured_stones_variety', 500)->nullable()->after('coloured_stones_species');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jewellery_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('jewellery_evaluations', 'coloured_stones_variety')) {
                $table->dropColumn('coloured_stones_variety');
            }
            if (Schema::hasColumn('jewellery_evaluations', 'coloured_stones_cut')) {
                $table->dropColumn('coloured_stones_cut');
            }
        });
    }
};
