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
        Schema::create('avatar_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id'); // Foreign key to users table
            $table->unsignedBigInteger('lead_id');
            $table->string('dialer_id');
            $table->string('AGE');
            $table->string('Smoker');
            $table->string('verifier');
            $table->string('center');
            $table->string('recordinglink2')->nullable();
            $table->unsignedBigInteger('list_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('campaign')->nullable();
            $table->string('closer')->nullable();
            $table->string('group_a')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('dispo')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('recording_filename')->nullable();
            $table->unsignedBigInteger('recording_id')->nullable();
            $table->text('recording_link')->nullable();
            $table->unsignedBigInteger('entry_list_id');
            $table->string('user_group')->nullable();
            $table->string('list_name')->nullable();
            $table->string('list_description')->nullable();
            $table->timestamp('entry_date')->default(now());
            $table->string('closer_name')->nullable();
            $table->string('dialername')->nullable();
            $table->string('centername')->nullable();
            $table->string('closer_status')->nullable()->default(null);

            $table->string('xferSubmission')->nullable();

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('agent_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatar_leads');
    }
};
