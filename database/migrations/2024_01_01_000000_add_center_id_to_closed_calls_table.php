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
            // Numeric reference to a center (nullable to avoid breaking existing rows)
            $table->unsignedBigInteger('center_id')->nullable()->after('center_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('closed_calls', function (Blueprint $table) {
            $table->dropColumn('center_id');
        });
    }
};

