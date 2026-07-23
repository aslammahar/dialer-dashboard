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
            $table->text('closer_assigned_history')->nullable()->after('assigned_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropColumn('closer_assigned_history');
        });
    }
};