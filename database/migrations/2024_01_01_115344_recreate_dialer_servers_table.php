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
        // Drop the existing problematic table
        Schema::dropIfExists('dialers_servers');

        // Create the new table with proper schema
        Schema::create('dialers_servers', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('dialer_name')->nullable();
            $table->string('server_no')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('folder_name')->nullable();
            $table->enum('server_status', ['0', '1'])->default('0');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dialers_servers');
    }
};