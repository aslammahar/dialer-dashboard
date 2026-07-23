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
        // ✅ Just added DEFAULT 'pending' at the end
        DB::statement("ALTER TABLE `closed_calls` MODIFY COLUMN `status` ENUM('pending','approved','DNF','Cancelled','NSF','DNC','Underwriting','Need to Reach','rejected','funded','charged_backed','Potential Lapsed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NULL (no default)
        DB::statement("ALTER TABLE `closed_calls` MODIFY COLUMN `status` ENUM('pending','approved','DNF','Cancelled','NSF','DNC','Underwriting','Need to Reach','rejected','funded','charged_backed','Potential Lapsed') NULL");
    }
};