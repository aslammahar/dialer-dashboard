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
        if (!Schema::hasColumn('avatar_leads', 'center_id')) {
            Schema::table('avatar_leads', function (Blueprint $table) {
                $table->unsignedBigInteger('center_id')
                    ->nullable()
                    ->after('agent_id');
                // No foreign key for now (same approach as users.center_id)
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('avatar_leads', 'center_id')) {
            Schema::table('avatar_leads', function (Blueprint $table) {
                $table->dropColumn('center_id');
            });
        }
    }
};

