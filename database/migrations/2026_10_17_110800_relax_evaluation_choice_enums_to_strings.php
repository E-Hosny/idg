<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diamond_evaluations')) {
            $diamondColumns = [
                'laser_inscription',
                'hpht_screen',
                'cvd_check',
                'polish',
                'symmetry',
                'cut',
                'clarity',
                'colour',
                'fluorescence_strength',
                'result',
                'stone_type',
                'report_done',
                'label_done',
            ];

            foreach ($diamondColumns as $column) {
                DB::statement("ALTER TABLE `diamond_evaluations` MODIFY `{$column}` VARCHAR(255) NULL");
            }
        }

        if (Schema::hasTable('jewellery_evaluations')) {
            $jewelleryColumns = [
                'hpht_screen',
                'cvd_check',
                'need_unmount',
                'coloured_stones_conclusion',
                'report_done',
                'label_done',
            ];

            foreach ($jewelleryColumns as $column) {
                if (Schema::hasColumn('jewellery_evaluations', $column)) {
                    DB::statement("ALTER TABLE `jewellery_evaluations` MODIFY `{$column}` VARCHAR(255) NULL");
                }
            }
        }
    }

    public function down(): void
    {
        // Irreversible safely: custom "Other" values may not fit original enums.
    }
};
