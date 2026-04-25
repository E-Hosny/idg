<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artifact_evaluations', function (Blueprint $table) {
            if (! Schema::hasColumn('artifact_evaluations', 'item_id')) {
                $table->string('item_id', 255)->nullable()->after('test_location');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'image1_path')) {
                $table->string('image1_path', 500)->nullable()->after('analytical_interpretation');
            }
            if (! Schema::hasColumn('artifact_evaluations', 'image2_path')) {
                $table->string('image2_path', 500)->nullable()->after('image1_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artifact_evaluations', function (Blueprint $table) {
            $columns = ['item_id', 'image1_path', 'image2_path'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('artifact_evaluations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

