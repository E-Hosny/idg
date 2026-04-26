<?php

use App\Models\JewelleryEvaluation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add optional detail text for the Octagonal side-stone shape; migrate legacy "Octagon" to "Octagonal".
     */
    public function up(): void
    {
        Schema::table('jewellery_evaluations', function (Blueprint $table) {
            if (! Schema::hasColumn('jewellery_evaluations', 'side_stones_shape_octagonal_detail')) {
                $table->string('side_stones_shape_octagonal_detail', 255)->nullable()->after('side_stones_clarities');
            }
        });

        JewelleryEvaluation::query()->each(function (JewelleryEvaluation $evaluation) {
            $changed = false;
            if (is_array($evaluation->side_stones_shapes)) {
                $shapes = $evaluation->side_stones_shapes;
                $shapes = array_map(function ($s) {
                    return $s === 'Octagon' ? 'Octagonal' : $s;
                }, $shapes);
                if ($shapes !== $evaluation->side_stones_shapes) {
                    $evaluation->side_stones_shapes = array_values($shapes);
                    $changed = true;
                }
            }
            if ($evaluation->centre_stone_shape === 'Octagon') {
                $evaluation->centre_stone_shape = 'Octagonal';
                $changed = true;
            }
            if ($changed) {
                $evaluation->save();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jewellery_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('jewellery_evaluations', 'side_stones_shape_octagonal_detail')) {
                $table->dropColumn('side_stones_shape_octagonal_detail');
            }
        });
    }
};
