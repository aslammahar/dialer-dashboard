<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoiceqaLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('voiceqa_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id'); // Foreign key to users table
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('dialer_id');
            $table->string('verifiers')->nullable();
            $table->string('recording');
            $table->string('GREETINGS');
            $table->string('PITCH_Call_About');
            $table->string('AGE');
            $table->string('Smoker');
            $table->string('Health1');
            $table->string('Beneficiary');
            $table->string('Account');
            $table->string('Plan');
            $table->string('Transfer_details');
            $table->string('Xfer_Consent')->nullable();
            $table->string('Rebuttals');
            $table->text('COMMENTS')->nullable();
            $table->string('Status');
            $table->string('QA_Person');
            $table->integer('Use_of_Rebuttals');
            $table->integer('No_of_Refusals');
            $table->integer('count');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('agent_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('voiceqa_leads');
    }
}

