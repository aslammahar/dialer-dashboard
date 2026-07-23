<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('avatar_leads', 'team_id')) {
            Schema::table('avatar_leads', function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->nullable()->after('agent_id');
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('avatar_leads', 'team_id')) {
            Schema::table('avatar_leads', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            });
        }
    }
};
