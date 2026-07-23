<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('avatar_monitoring', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('employee_id');
        $table->unsignedBigInteger('filled_by'); // To store the user who filled the form
        $table->time('monitor_from')->nullable();
        $table->time('monitor_to')->nullable();
        $table->date('monitor_date')->nullable();
        $table->text('greeting')->nullable();
        $table->text('response_on_answering_machine')->nullable();
        $table->text('response_time')->nullable();
        $table->text('customer_response')->nullable();
        $table->text('leave_3_way')->nullable();
        $table->text('questions')->nullable();
        $table->text('dispositions')->nullable();
        $table->text('comments_suggestions')->nullable();
        $table->json('disposition_records')->nullable(); // Store serial number, lead ID, and dropdown
        $table->string('score')->nullable();
        $table->json('notify_to')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatar_monitoring');
    }
};
