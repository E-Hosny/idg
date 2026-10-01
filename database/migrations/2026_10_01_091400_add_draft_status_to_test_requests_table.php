<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `test_requests` CHANGE `status` `status` ENUM('draft', 'pending', 'under_evaluation', 'evaluated', 'certified', 'delivered', 'signed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('test_requests')->where('status', 'draft')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE `test_requests` CHANGE `status` `status` ENUM('pending', 'under_evaluation', 'evaluated', 'certified', 'delivered', 'signed') NOT NULL DEFAULT 'pending'");
    }
};
