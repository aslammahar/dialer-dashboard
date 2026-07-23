<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: teams table mein hc_override column add karta hai.
 *
 * Run karein:
 *   php artisan migrate
 *
 * hc_override = NULL  →  auto mode: actual agent count use hoga
 * hc_override = 30    →  manual mode: average 30 se nikly ga chahe agents 28 hon ya 32
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedSmallInteger('hc_override')
                  ->nullable()
                  ->default(null)
                  ->after('leader_id')
                  ->comment('Manual HC override. NULL = actual agent count use hoga.');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('hc_override');
        });
    }
};