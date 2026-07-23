<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_no');
            $table->string('email')->nullable();
            $table->string('experience')->nullable();
            $table->string('location')->nullable();
            $table->string('interview_taken_by')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('age')->nullable();
            $table->string('alternate_number')->nullable();
            $table->date('date')->nullable();
            $table->string('emergency_number')->nullable();
            $table->string('relation')->nullable();
            $table->string('formid')->unique()->nullable();
            $table->string('pin')->unique()->nullable();

            // Questions
            $table->boolean('willing_to_work_night_shift')->nullable();
            $table->boolean('telemarketing_experience')->nullable();
            $table->string('total_work_experience')->nullable();
            $table->text('job_reason')->nullable();
            $table->boolean('currently_student')->nullable();
            $table->text('strength')->nullable();
            $table->text('weakness')->nullable();
            $table->text('self_description')->nullable();

            // Dropdowns
            $table->string('source')->nullable(); // Instagram, Facebook, etc.
            $table->string('work_from')->nullable(); // Home, office J.sons, etc.
            $table->string('designation')->nullable(); // Avatar-CSR, Voice-CSR, etc.
            $table->string('status')->nullable(); // No Answer, Not Interested, etc.
            $table->string('interviewer_remarks')->nullable(); // Excellent, Good, etc.

            // Scores
            $table->integer('communication_score')->nullable();
            $table->integer('accent_score')->nullable();
            $table->integer('energy_score')->nullable();
            $table->integer('comprehension_score')->nullable();
            $table->integer('experience_score')->nullable();

            // Hired fields
            $table->text('hired_comments')->nullable();
            $table->string('project_assigned')->nullable();
            $table->string('signing_bonus')->nullable();
            $table->string('salary_expectation')->nullable();
            $table->string('final_status')->nullable();
            $table->date('joining_date')->nullable();

            // Rejected fields
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_comments')->nullable();
            $table->integer('rejection_communication')->nullable();
            $table->integer('rejection_energy')->nullable();
            $table->integer('rejection_accent')->nullable();
            $table->integer('rejection_comprehension')->nullable();
            $table->unsignedBigInteger('refer_to')->nullable();
            $table->foreign('refer_to')->references('id')->on('employees')->onDelete('cascade');
            $table->boolean('offer_letter_sent')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropForeign(['refer_to']);
            $table->dropColumn('refer_to');
            $table->dropColumn('offer_letter_sent');
        });
    }
}