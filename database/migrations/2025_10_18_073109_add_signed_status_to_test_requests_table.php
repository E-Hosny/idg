<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // لتحديث enum في MySQL نحتاج لاستخدام raw SQL
        DB::statement("ALTER TABLE `test_requests` CHANGE `status` `status` ENUM('pending', 'under_evaluation', 'evaluated', 'certified', 'delivered', 'signed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // عكس التغيير - إزالة signed من enum
        DB::statement("ALTER TABLE `test_requests` CHANGE `status` `status` ENUM('pending', 'under_evaluation', 'evaluated', 'certified', 'delivered') NOT NULL DEFAULT 'pending'");
    }
};