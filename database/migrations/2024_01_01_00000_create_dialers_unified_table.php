<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Merged table: all columns from dialerlist_tb and dialers_servers.
     * Common column "recording_link" and timestamps appear once.
     */
    public function up(): void
    {
        Schema::create('dialers_unified', function (Blueprint $table) {
            $table->id();

            // Columns from dialerlist_tb
            $table->string('dialer_ip', 150)->nullable();
            $table->string('dialer_weblink', 150)->nullable();
            $table->string('dialer_access', 300)->nullable();
            $table->string('dialer_no', 40)->nullable();
            $table->string('dialer_team', 40)->nullable();

            // Columns from dialers_servers
            $table->string('dialer_name')->nullable();
            $table->string('server_no')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('folder_name')->nullable();
            $table->boolean('server_status')->default(false)->nullable();

            // Common column (both tables have recording_link) — single column
            $table->text('recording_link')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialers_unified');
    }
};
