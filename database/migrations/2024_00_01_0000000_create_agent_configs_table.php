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
        Schema::create('agent_configs', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name')->index();
            $table->integer('advance_months'); // Total months paid in advance (e.g., 12, 6)
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate agent configs
            $table->unique('agent_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_commission_configs');
    }
};