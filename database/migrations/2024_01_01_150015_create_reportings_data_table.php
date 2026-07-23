<?php
// database/migrations/xxxx_xx_xx_create_reporting_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reporting_data', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id'); // EMP0000093, etc.
            $table->date('report_date'); // Date for the record
            $table->string('name')->nullable(); // Employee name
            
            // Basic fields stored in DB (from uploads)
            $table->string('talktime')->nullable(); // H:M:S format
            $table->integer('talktime_seconds')->default(0); // For calculations
            $table->string('avg_talktime')->nullable(); // H:M:S format  
            $table->integer('avg_talktime_seconds')->default(0); // For calculations
            $table->integer('total_avatar_jcs_xfers')->default(0);
            $table->integer('avatar_xfer')->default(0);
            $table->integer('jcs_xfers')->default(0);
            
            // Additional fields that will be populated from other Excel files
            $table->integer('working_days')->default(0);
            $table->integer('late_min')->default(0);
            $table->integer('total_submitted_sales')->default(0);
            $table->integer('underwriting_ho')->default(0);
            $table->integer('total_approved')->default(0);
            $table->decimal('average_approved', 8, 2)->default(0);
            $table->decimal('premium_approved_spd', 10, 2)->default(0);
            $table->decimal('total_conv_calls_submission', 5, 2)->default(0); // Percentage
            $table->decimal('total_conv_approved_submission', 5, 2)->default(0); // Percentage
            
            // Avatar specific fields
            $table->integer('avatar_xfer_submitted_sales')->default(0);
            $table->integer('avatar_xfer_approved_sales')->default(0);
            $table->decimal('avatar_xfer_conv_calls_submission', 5, 2)->default(0); // Percentage
            $table->decimal('avatar_xfer_conv_approved_submission', 5, 2)->default(0); // Percentage
            
            // JCs specific fields
            $table->integer('jcs_submitted')->default(0);
            $table->integer('jcs_approved')->default(0);
            $table->decimal('jcs_conv_calls_submission', 5, 2)->default(0); // Percentage
            $table->decimal('jcs_conv_approved_submission', 5, 2)->default(0); // Percentage
            
            // Call duration fields
            $table->integer('calls_dur_less_than_200_secs')->default(0);
            $table->integer('calls_dur_between_200_400_secs')->default(0);
            $table->integer('calls_dur_greater_than_400_secs')->default(0);
            $table->string('rec_1_200_sec_duration')->nullable(); // H:M:S format
            $table->string('rec_2_400_sec_duration')->nullable(); // H:M:S format
            $table->string('rec_3_600_sec_duration')->nullable(); // H:M:S format
            
            $table->timestamps();
            
            // Composite unique key for employee_id and report_date
            $table->unique(['employee_id', 'report_date']);
            $table->index('employee_id');
            $table->index('report_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reporting_data');
    }
};