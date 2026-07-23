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
        Schema::table('closed_calls', function (Blueprint $table) {
            // Add 3 new optional fields
            $table->string('recording_id')->nullable()->after('lead_id');
            $table->string('hippa_id')->nullable()->after('recording_id');
            $table->string('policy_id')->nullable()->after('hippa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropColumn([
                'recording_id',
                'hippa_id',
                'policy_id'
            ]);
        });
    }
};