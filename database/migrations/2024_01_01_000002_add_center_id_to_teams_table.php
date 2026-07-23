<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('teams', 'center_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')
                    ->nullable()
                    ->after('leader_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('teams', 'center_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('center_id');
            });
        }
    }
};
