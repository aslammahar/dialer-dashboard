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
        Schema::create('team_agent_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_agent_id'); // Reference to team_agents table
            $table->unsignedBigInteger('updated_by')->nullable(); // Who updated it?
            $table->string('event_type'); // created, updated, deleted
            $table->json('old_values')->nullable(); // Store previous values
            $table->json('new_values')->nullable(); // Store new values
            $table->timestamps();

            $table->foreign('team_agent_id')->references('id')->on('team_agent')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_agent_audits');
    }
};
