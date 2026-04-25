<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artifact_evaluations', function (Blueprint $table) {
            if (! Schema::hasColumn('artifact_evaluations', 'shape')) {
                $table->string('shape', 100)->nullable()->after('measurements');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'cut')) {
                $table->string('cut', 100)->nullable()->after('shape');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'stone_group')) {
                $table->string('stone_group', 100)->nullable()->after('result');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'species')) {
                $table->string('species', 100)->nullable()->after('stone_group');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'treatment')) {
                $table->string('treatment', 255)->nullable()->after('species_group');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'gemstone_type')) {
                $table->string('gemstone_type', 50)->nullable()->after('treatment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artifact_evaluations', function (Blueprint $table) {
            $columns = ['shape', 'cut', 'stone_group', 'species', 'treatment', 'gemstone_type'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('artifact_evaluations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

