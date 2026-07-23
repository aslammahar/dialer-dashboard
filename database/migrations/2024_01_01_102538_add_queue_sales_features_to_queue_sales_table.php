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
        // Add new columns to the existing 'queue_sales' table
        Schema::table('queue_sales', function (Blueprint $table) {
            // New field for the 'Connect' feature
            $table->boolean('is_connected')->default(false)->after('clients_id');
            $table->timestamp('connected_at')->nullable()->after('is_connected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_sales', function (Blueprint $table) {
            $table->dropColumn(['is_connected', 'connected_at']);
        });
    }
};