<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvatarQALeadsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avatar_q_a_leads', function (Blueprint $table) {
            $table->id();
            $table->string('agent_email', 191); // Set data type to VARCHAR(191)
            $table->string('phone_number')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('lead_id')->nullable();
            $table->string('dialer_id')->nullable();
            $table->string('verifiers')->nullable();
            $table->string('recording')->nullable();
            $table->string('greetings')->nullable();
            $table->string('pitch_call_about')->nullable();
            $table->string('pitch')->nullable();
            $table->string('age')->nullable();
            $table->string('smoker')->nullable();
            $table->string('health1')->nullable();
            $table->string('health')->nullable();
            $table->string('beneficiary')->nullable();
            $table->string('account')->nullable();
            $table->string('plan')->nullable();
            $table->string('transfer_details')->nullable();
            $table->string('xfer_consent')->nullable();
            $table->string('rebuttals')->nullable();
            $table->text('comments')->nullable();
            $table->text('recording_link')->nullable();
            $table->string('status')->nullable();
            $table->string('qa_person')->nullable();
            $table->string('closer_name')->nullable();
            $table->string('qa_comment')->nullable();
            $table->integer('rebuttal')->nullable();
            $table->integer('use_of_rebuttals')->nullable();
            $table->integer('total_rebuttal')->nullable();
            $table->integer('qa_timestamp')->nullable();
            $table->integer('total_duration')->nullable();
            $table->integer('played_duration')->nullable();
            $table->text('call_status')->nullable();
            $table->integer('no_of_refusals')->nullable();
            $table->integer('total_refusal')->nullable();
            $table->integer('count')->nullable();
            // $table->dateTime('date_time')->nullable();
            $table->dateTime('xferSubmissionTime')->nullable();
            $table->timestamps();
             
            $table->foreign('agent_email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatar_q_a_leads');
    }
}
