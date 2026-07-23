<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('full_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('pseudo_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('team_leader')->nullable();
            $table->string('cnic_number')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('source_of_joining')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('city')->nullable();
            $table->string('designation')->nullable();
            $table->string('work_from')->nullable();
            $table->string('employee_id')->unique()->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_details');
    }
};