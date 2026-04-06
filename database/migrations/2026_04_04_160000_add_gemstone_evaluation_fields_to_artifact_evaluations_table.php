<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * General / colored gemstone evaluation fields used by Evaluate.vue + storeGeneralEvaluation().
     */
    public function up(): void
    {
        Schema::table('artifact_evaluations', function (Blueprint $table) {
            if (! Schema::hasColumn('artifact_evaluations', 'test_date')) {
                $table->date('test_date')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'test_location')) {
                $table->string('test_location', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'weight')) {
                $table->decimal('weight', 12, 3)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'colour')) {
                $table->string('colour', 100)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'transparency')) {
                $table->string('transparency', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'lustre')) {
                $table->string('lustre', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'tone')) {
                $table->string('tone', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'phenomena')) {
                $table->string('phenomena', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'saturation')) {
                $table->string('saturation', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'measurements')) {
                $table->string('measurements', 100)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'shape_cut')) {
                $table->string('shape_cut', 100)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'pleochroism')) {
                $table->string('pleochroism', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'optic_character')) {
                $table->string('optic_character', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'refractive_index')) {
                $table->json('refractive_index')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'ri_result')) {
                $table->string('ri_result', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'inclusion')) {
                $table->text('inclusion')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'weight_air')) {
                $table->decimal('weight_air', 12, 3)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'weight_water')) {
                $table->decimal('weight_water', 12, 3)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'sg_result')) {
                $table->string('sg_result', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'fluorescence_long')) {
                $table->string('fluorescence_long', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'fluorescence_short')) {
                $table->string('fluorescence_short', 50)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'result')) {
                $table->string('result', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'variety')) {
                $table->string('variety', 100)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'species_group')) {
                $table->string('species_group', 100)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'comments')) {
                $table->text('comments')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'grader_name')) {
                $table->string('grader_name', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'grader_date')) {
                $table->date('grader_date')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'analytical_interpretation')) {
                $table->text('analytical_interpretation')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'retaining_place')) {
                $table->string('retaining_place', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'retained_by')) {
                $table->string('retained_by', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'retained_date')) {
                $table->date('retained_date')->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'report_done')) {
                $table->boolean('report_done')->nullable()->default(false);
            }
            if (! Schema::hasColumn('artifact_evaluations', 'label_done')) {
                $table->boolean('label_done')->nullable()->default(false);
            }
            if (! Schema::hasColumn('artifact_evaluations', 'checked_by')) {
                $table->string('checked_by', 255)->nullable();
            }
            if (! Schema::hasColumn('artifact_evaluations', 'checked_date')) {
                $table->date('checked_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('artifact_evaluations', function (Blueprint $table) {
            $cols = [
                'test_date', 'test_location', 'weight', 'colour', 'transparency', 'lustre', 'tone',
                'phenomena', 'saturation', 'measurements', 'shape_cut', 'pleochroism', 'optic_character',
                'refractive_index', 'ri_result', 'inclusion', 'weight_air', 'weight_water', 'sg_result',
                'fluorescence_long', 'fluorescence_short', 'result', 'variety', 'species_group', 'comments',
                'grader_name', 'grader_date', 'analytical_interpretation', 'retaining_place', 'retained_by',
                'retained_date', 'report_done', 'label_done', 'checked_by', 'checked_date',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('artifact_evaluations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
