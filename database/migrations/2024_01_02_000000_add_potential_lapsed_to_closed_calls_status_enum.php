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
        DB::statement("ALTER TABLE `closed_calls` MODIFY COLUMN `status` ENUM('pending','approved','DNF','Cancelled','NSF','DNC','Underwriting','Need to Reach','rejected','funded','charged_backed','Potential Lapsed') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `closed_calls` MODIFY COLUMN `status` ENUM('pending','approved','DNF','Cancelled','NSF','DNC','Underwriting','Need to Reach','rejected','funded','charged_backed') NULL");
    }
};